# List Akun Demo Aplikasi

Berikut adalah daftar akun yang dapat digunakan untuk login ke dalam aplikasi. Akun ini dibuat secara otomatis jika Anda telah menjalankan perintah migrasi beserta dataseeder (`php artisan migrate --seed`).

### 1. Akun Super Admin
Akun ini digunakan oleh pemilik platform (Platform Owner) yang dapat memantau atau mengelola sistem dari level paling tinggi, termasuk mengontrol semua tenant yang ada.
- **Email**: `superadmin@paper.test`
- **Password**: `password`

### 2. Akun Tenant Admin (Owner Perusahaan / Admin Demo)
Akun ini merupakan profil contoh untuk pemilik utama dari suatu perusahaan/tenant (dalam hal ini PT Demo Company) di sistem aplikasi (Model SaaS/Multi-tenant).
- **Email**: `admin@demo-company.com`
- **Password**: `password`

### 3. Akun Staff Keuangan (Finance User)
Akun ini berguna jika Anda ingin mencoba level akses yang lebih terbatas yang dikhususkan sebagai bagian *finance* (Keuangan) di dalam salah satu tenant (PT Demo Company).
- **Email**: `finance@demo-company.com`
- **Password**: `password`
