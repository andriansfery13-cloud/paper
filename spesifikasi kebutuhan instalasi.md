# Spesifikasi Kebutuhan Instalasi (System Requirements)

Berikut adalah spesifikasi sistem minimum yang dibutuhkan untuk menginstal dan menjalankan aplikasi ini dengan baik:

## Kebutuhan Server / Environment Lokal
1. **Sistem Operasi**: Windows, Linux, atau macOS (bisa menggunakan aplikasi *all-in-one* seperti Laragon, XAMPP, atau MAMP).
2. **Web Server**: Apache atau Nginx.
3. **PHP**: Versi **7.3** atau **8.0** (Sangat disarankan memakai PHP 8.0 untuk kompatibilitas package).
4. **Database**: MySQL versi 5.7+ / 8.0+ atau MariaDB versi 10.3+.

## Kebutuhan Ekstensi PHP
Penyedia web server/PHP Anda harus mengaktifkan beberapa ekstensi PHP standar ekosistem Laravel:
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD Library (opsional, namun direkomendasikan untuk fitur spesifik seperti QRCode / PDF).

## Manajer Paket (Package Manager)
- **Composer**: Wajib terinstal di sistem Anda untuk mengelola *dependencies* (pustaka PHP) yang terdaftar di `composer.json`.

## Library / Package Spesifik Aplikasi
Aplikasi ini sudah membundel / bergantung pada beberapa package penting yang akan diunduh sewaktu Instalasi Composer, yaitu:
- Framework `laravel/framework` (^8.75)
- Sistem Perizinan / Role-based Access `spatie/laravel-permission`
- Integrasi Payment Gateway `midtrans/midtrans-php`
- Pembuat Dokumen PDF `barryvdh/laravel-dompdf`
- Export / Import Excel `maatwebsite/excel`
- Generator Kode QR `simplesoftwareio/simple-qrcode`
