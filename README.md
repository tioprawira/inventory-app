# Inventory App

Sistem Inventory Barang berbasis PHP untuk membantu pengelolaan stok barang, barang masuk, barang keluar, kategori, serta laporan inventory dengan tampilan modern menggunakan Bootstrap.

## 🚀 Fitur Utama

* Login multi user (Admin & Staff)
* Dashboard admin dan staff
* Manajemen data barang
* Manajemen kategori barang
* Data barang masuk
* Data barang keluar
* Grafik statistik inventory
* Export laporan Excel menggunakan PhpSpreadsheet
* Export PDF menggunakan TCPDF
* Tampilan responsive dengan Bootstrap 5
* Sistem autentikasi session login

---

## 🛠️ Teknologi yang Digunakan

* PHP Native
* MySQL
* Bootstrap 5
* PhpSpreadsheet
* TCPDF
* Composer

---

## 📂 Struktur Folder

```bash
inventory-app/
│
├── admin/              # Halaman dashboard admin
├── assets/             # Asset CSS, JS, gambar
├── auth/               # Sistem login & autentikasi
├── config/             # Konfigurasi database
├── pages/              # Halaman fitur inventory
├── staff/              # Dashboard staff
├── templates/          # Header & footer template
├── vendor/             # Dependency composer
├── composer.json
├── inventory_db.sql    # Database SQL
└── index.php
```

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/inventory-app.git
```

### 2. Masuk ke Folder Project

```bash
cd inventory-app
```

### 3. Install Dependency Composer

```bash
composer install
```

### 4. Import Database

* Buat database baru di MySQL
* Import file:

```bash
inventory_db.sql
```

### 5. Konfigurasi Database

Edit file:

```bash
config/database.php
```

Sesuaikan:

```php
$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = '';
```

### 6. Jalankan Project

Simpan project di folder:

```bash
htdocs/
```

Lalu akses:

```bash
http://localhost/inventory-app
```

---

## 🔐 Role User

### Admin

* Kelola seluruh data inventory
* Kelola barang masuk & keluar
* Melihat dashboard statistik
* Export laporan

### Staff

* Melihat dashboard staff
* Mengelola data tertentu sesuai hak akses

---

## 📊 Library Tambahan

### PhpSpreadsheet

Digunakan untuk export data Excel.

### TCPDF

Digunakan untuk generate laporan PDF.

---

## 📸 Screenshot

Tambahkan screenshot aplikasi di sini.

```bash
assets/img/screenshot-dashboard.png
```

---

## 📌 Requirement

* PHP >= 8.0
* MySQL
* Composer
* Apache / XAMPP / Laragon

---

## 🤝 Kontribusi

Pull request dan kontribusi sangat terbuka.

1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

---

## 📄 License

Project ini menggunakan lisensi MIT.

---

## 👨‍💻 Developer

Dikembangkan untuk kebutuhan sistem inventory barang modern berbasis web.
