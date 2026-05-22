<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
            }

            // 2FA for Company Admin (Owner)
            if ($user->is_owner && !$user->isSuperAdmin()) {
                Auth::logout();
                $request->session()->put('auth.2fa_pending', $user->id);
                $request->session()->put('auth.remember', $remember);

                $this->sendLoginOtp($user);

                return redirect()->route('verify.otp.form');
            }

            // Check tenant status for tenant users
            if ($user->isTenantUser() && $user->tenant) {
                if (!$user->tenant->isActive()) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Akun perusahaan Anda telah dinonaktifkan.']);
                }
            }

            // Update last login
            $user->updateLastLogin();

            $request->session()->regenerate();

            // Redirect based on user type
            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('auth.register', compact('plans'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Create tenant
        $slug = Str::slug($request->company_name);
        $originalSlug = $slug;
        $counter = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Auto assign Free plan
        $plan = SubscriptionPlan::where('slug', 'free')->first();

        // Status inactive until email verified
        $tenant = Tenant::create([
            'company_name' => $request->company_name,
            'slug' => $slug,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'inactive',
            'current_plan_id' => $plan ? $plan->id : null,
            'trial_ends_at' => $plan && $plan->trial_days > 0
                ? now()->addDays($plan->trial_days)
                : null,
            'token_balance' => $plan ? $plan->included_tokens : 0,
        ]);

        // Create admin user (inactive)
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'tenant_user',
            'is_owner' => true,
            'is_active' => false,
            'email_verified_at' => null,
        ]);

        // Assign owner role
        $user->assignRole('owner');

        // Send Verification Email
        $user->sendEmailVerificationNotification();

        return redirect()->route('registration.success');
    }

    /**
     * Handle Email Verification
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $id, (string) $user->getKey())) {
            return redirect()->route('login')->withErrors(['email' => 'Link verifikasi tidak valid.']);
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->withErrors(['email' => 'Link verifikasi tidak valid atau kadaluarsa.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('message', 'Email sudah terverifikasi. Silakan login.');
        }

        if ($user->markEmailAsVerified()) {
            $user->update(['is_active' => true]);

            if ($user->tenant) {
                $user->tenant->update(['status' => 'active']);
            }

            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Auto login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil diaktifkan! Selamat datang.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        if (!session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }

        $otp = session('auth.otp');
        $expiry = session('auth.otp_expiry');

        if (!$otp || !$expiry || now()->greaterThan($expiry)) {
            return back()->withErrors(['otp' => 'Kode OTP kadaluarsa. Silakan kirim ulang.']);
        }

        if ($request->otp != $otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        // Login
        $userId = session('auth.2fa_pending');
        $remember = session('auth.remember', false);

        Auth::loginUsingId($userId, $remember);
        $user = Auth::user();

        // Clear session
        session()->forget(['auth.2fa_pending', 'auth.remember', 'auth.otp', 'auth.otp_expiry']);

        $user->updateLastLogin();
        $request->session()->regenerate();

        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('auth.2fa_pending')) {
            return redirect()->route('login');
        }

        $userId = session('auth.2fa_pending');
        $user = User::find($userId);

        if ($user) {
            $via = $request->input('via', 'any');
            $sentChannel = $this->sendLoginOtp($user, $via);

            if ($sentChannel) {
                return back()->with('message', "Kode OTP baru telah dikirim via $sentChannel.");
            }

            $errorMsg = ($via === 'any')
                ? 'Gagal mengirim OTP. Hubungi Admin.'
                : 'Gagal mengirim via ' . ucfirst($via) . '. Pastikan pengaturan aktif atau coba metode lain.';

            return back()->withErrors(['otp' => $errorMsg]);
        }

        return redirect()->route('login');
    }

    /**
     * Send Login OTP
     * Returns channel name if sent, null otherwise
     */
    protected function sendLoginOtp($user, $channel = 'any')
    {
        $otp = rand(100000, 999999);
        session([
            'auth.otp' => $otp,
            'auth.otp_expiry' => now()->addMinutes(10)
        ]);

        $sentViaWa = false;
        $successfulChannel = null;

        // Try WhatsApp first if enabled in Tenant Settings
        if ($user->tenant) {
            $settings = $user->tenant->settings;
            // Robust check for boolean
            $waEnabledRaw = $settings['notifications']['whatsapp']['enabled'] ?? false;
            $waEnabled = filter_var($waEnabledRaw, FILTER_VALIDATE_BOOLEAN) || $waEnabledRaw === 'true';

            // Prioritize Tenant Phone (Company Phone) for "Admin Perusahaan" OTP
            $phone = $user->tenant->phone;
            if (empty($phone)) {
                $phone = $user->phone;
            }

            \Illuminate\Support\Facades\Log::info("2FA Attempt for User {$user->id}. Channel: {$channel}. WA Settings Enabled? " . ($waEnabled ? 'Yes' : 'No') . ". Phone: {$phone}");

            // Send via WhatsApp if requested OR if 'any' - TRUST NotificationService to check Global/Tenant
            if (($channel === 'any' || $channel === 'whatsapp') && $phone) {
                $notificationService = new NotificationService();
                $message = "🔒 *Kode OTP Login*\n\nKode OTP Anda adalah: *{$otp}*\n\nJangan berikan kode ini kepada siapapun.";
                $sentViaWa = $notificationService->sendWhatsApp($user->tenant, $phone, $message);

                if ($sentViaWa) {
                    $successfulChannel = 'WhatsApp';
                }

                \Illuminate\Support\Facades\Log::info("2FA WA Sent Status: " . ($sentViaWa ? 'Success' : 'Failed'));
            }
        }

        // Send via Email if requested OR if 'any' and WA failed
        if ($channel === 'email' || ($channel === 'any' && !$sentViaWa)) {
            try {
                Mail::raw("Kode OTP Login Anda: $otp\n\nJangan berikan kode ini kepada siapapun.", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Kode OTP Login');
                });
                $successfulChannel = 'Email';
                \Illuminate\Support\Facades\Log::info("2FA Email Sent to {$user->email}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("2FA Email Failed: " . $e->getMessage());
            }
        }

        return $successfulChannel;
    }
}
