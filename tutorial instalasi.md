# Tutorial Instalasi Aplikasi

Berikut adalah langkah-langkah untuk melakukan instalasi aplikasi secara lokal (direkomendasikan menggunakan Laragon atau XAMPP):

1. **Persiapan Direktori**
   Pastikan folder project (dalam hal ini `paper`) sudah berada di dalam folder *document root* web server Anda (contoh: `c:\laragon\www\paper` jika menggunakan Laragon, atau `c:\xampp\htdocs\paper` jika menggunakan XAMPP).

2. **Buka Terminal/Command Prompt**
   Buka terminal atau command prompt dan arahkan ke direktori project Anda.
   ```bash
   cd c:\laragon\www\paper
   ```

3. **Install Dependencies (Composer)**
   Jalankan perintah berikut untuk menginstall seluruh *library* PHP yang dibutuhkan aplikasi:
   ```bash
   composer install
   ```

4. **Konfigurasi Environment (.env)**
   - Copy file `.env.example` menjadi `.env`
     ```bash
     copy .env.example .env
     ```
   - Buka file `.env` di text editor.
   - Sesuaikan konfigurasi database Anda. Ubah bagian berikut:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nama_database_anda
     DB_USERNAME=root
     DB_PASSWORD=
     ```
     *(Pastikan Anda sudah membuat database kosong dengan nama yang sesuai di phpMyAdmin/HeidiSQL)*

5. **Generate Application Key**
   Jalankan perintah ini untuk menghasilkan kunci keamanan aplikasi:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database dan Seeder**
   Jalankan perintah ini untuk membuat struktur tabel di database beserta data awal (termasuk list akun demo):
   ```bash
   php artisan migrate --seed
   ```

7. **Akses Aplikasi**
   - Jika menggunakan **Laragon**, Anda dapat langsung mengakses aplikasi melalui browser dengan alamat: `http://paper.test`
   - Jika menggunakan **XAMPP**, Anda dapat mengaksesnya via alamat seperti: `http://localhost/paper/public`
   - Atau Anda dapat menggunakan perintah *serve* bawaan Laravel:
     ```bash
     php artisan serve
     ```
     Lalu buka browser dan akses `http://127.0.0.1:8000`
