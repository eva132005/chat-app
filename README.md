# Real-time Chat Application

## Deskripsi
Real-time Chat Application adalah aplikasi web yang memungkinkan pengguna untuk berkomunikasi secara langsung (real-time) menggunakan teknologi WebSocket. Aplikasi ini dibangun menggunakan framework Laravel 13 dengan Laravel Reverb sebagai WebSocket server.

## Fitur Utama
- **User Authentication** : Pengguna dapat melakukan registrasi, login, dan logout
- **Private Chat** : Pengguna dapat melakukan percakapan secara pribadi dengan pengguna lain
- **Group Chat** : Pengguna dapat membuat grup dan melakukan percakapan bersama
- **User Presence Tracking** : Sistem dapat mendeteksi status pengguna secara real-time (Online/Offline)
- **Hapus Pesan** : Pengguna dapat menghapus pesan yang telah dikirim

## Teknologi yang Digunakan
- **Framework** : Laravel 13
- **Bahasa Pemrograman** : PHP 8.3
- **Frontend** : Blade Template, Tailwind CSS
- **WebSocket** : Laravel Reverb
- **Database** : MySQL
- **Tools** : Laragon, Composer, Node.js, Git

## Persyaratan Sistem
- PHP >= 8.2
- Composer
- Node.js
- MySQL
- Laragon (disarankan)

## Langkah-langkah Instalasi

### 1. Clone Repository
Buka terminal dan jalankan perintah berikut:
```bash
git clone https://github.com/eva132005/chat-app.git
cd chat-app
```

### 2. Install Dependencies PHP
```bash
composer install
```

### 3. Install Dependencies Node.js
```bash
npm install
```

### 4. Salin File Environment
```bash
cp .env.example .env
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Konfigurasi Database
Buka file `.env` dan sesuaikan konfigurasi database:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_app
DB_USERNAME=root
DB_PASSWORD=
### 7. Jalankan Migration Database
```bash
php artisan migrate
```

### 8. Build Asset Frontend
```bash
npm run build
```

### 9. Menjalankan Aplikasi
Buka 3 terminal secara bersamaan dan jalankan perintah berikut:

**Terminal 1 - Web Server:**
```bash
php artisan serve
```

**Terminal 2 - WebSocket Server:**
```bash
php artisan reverb:start
```

**Terminal 3 - Queue Worker:**
```bash
php artisan queue:work
```

### 10. Akses Aplikasi
Buka browser dan akses alamat berikut:http://localhost:8000
## Cara Penggunaan
1. Buka aplikasi di browser
2. Klik **Register** untuk membuat akun baru
3. Isi nama, email, dan password kemudian klik **Register**
4. Setelah berhasil, akan diarahkan ke halaman utama chat
5. Pilih nama pengguna di sidebar untuk memulai **Private Chat**
6. Klik tombol **+ Buat Group** untuk membuat **Group Chat**
7. Ketik pesan dan tekan **Enter** atau klik tombol **Kirim**
8. Untuk menghapus pesan, klik tombol **✕** yang muncul di pojok pesan
9. Status **Online/Offline** pengguna ditampilkan secara real-time di sidebar