<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan.');
        }

        $users = $tenant->users()
            ->orderBy('is_owner', 'desc')
            ->orderBy('name')
            ->paginate(15);

        $plan = $tenant->currentPlan;
        $canAddUser = $tenant->canCreateUser();
        $userLimit = $plan ? $plan->max_users : 0;
        $userCount = $tenant->users()->count();

        return view('settings.users.index', compact('users', 'canAddUser', 'userLimit', 'userCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan.');
        }

        if (!$tenant->canCreateUser()) {
            return redirect()->route('settings.users.index')
                ->with('error', 'Kuota user telah habis. Silakan upgrade paket untuk menambah user.');
        }

        return view('settings.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan.');
        }

        if (!$tenant->canCreateUser()) {
            return response()->json([
                'success' => false,
                'quota_exceeded' => true,
                'message' => 'Kuota user telah habis.',
                'usage' => [
                    'users' => $tenant->users()->count() . '/' . ($tenant->currentPlan ? $tenant->currentPlan->max_users : 0)
                ]
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'tenant_user',
            'is_owner' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('settings.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || $user->tenant_id !== $tenant->id) {
            return redirect()->route('settings.users.index')->with('error', 'User tidak ditemukan.');
        }

        return view('settings.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || $user->tenant_id !== $tenant->id) {
            return redirect()->route('settings.users.index')->with('error', 'User tidak ditemukan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('settings.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant || $user->tenant_id !== $tenant->id) {
            return redirect()->route('settings.users.index')->with('error', 'User tidak ditemukan.');
        }

        if ($user->is_owner) {
            return redirect()->route('settings.users.index')
                ->with('error', 'Tidak dapat menghapus pemilik akun.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
