# 📚 Sistem Perpustakaan Digital - Laravel

Aplikasi manajemen perpustakaan berbasis web menggunakan Laravel 10.

## 👥 Pembagian Tugas Kelompok

| No | Anggota | Fitur | Bagian CRUD | Detail Tugas |
|----|---------|-------|-------------|--------------|
| 1 | Anggota 1 | Kelola Buku | **C + R** | Tambah buku, simpan buku, daftar buku, detail buku |
| 2 | Anggota 2 | Kelola Buku | **U + D** | Edit buku, update buku, hapus buku, update cover |
| 3 | Anggota 3 | Kelola Kategori | **C + R** | Tambah kategori, simpan, daftar, detail kategori |
| 4 | Anggota 4 | Kelola Kategori | **U + D** | Edit kategori, update, hapus kategori |
| 5 | Anggota 5 | Peminjaman | **C + R** | Form pinjam, simpan, daftar, detail peminjaman |
| 6 | Anggota 6 | Peminjaman | **U + D** | Edit, update status, hapus, proses pengembalian |

---

## 🗄️ Struktur Database

```
users               → id, name, email, password, role (admin/member)
members             → id, user_id, member_code, phone, address, birth_date, status
categories          → id, name, description
books               → id, title, author, isbn, category_id, stock, year, publisher, description, cover
borrowings          → id, member_id, book_id, borrow_date, due_date, status
returns             → id, borrowing_id, return_date, fine, notes
```

## 🔗 Relasi Antar Tabel

```
User      hasOne  Member
Member    belongsTo  User
Member    hasMany Borrowing

Category  hasMany Book
Book      belongsTo Category

Book      hasMany Borrowing
Borrowing belongsTo Book
Borrowing belongsTo Member

Borrowing hasOne  ReturnBook
ReturnBook belongsTo Borrowing
```

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Clone / Download Project

```bash
git clone https://github.com/[username]/perpustakaan.git
cd perpustakaan
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan konfigurasi database:

```env
DB_DATABASE=perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database

Buat database baru di MySQL/phpMyAdmin bernama `perpustakaan`.

### 5. Jalankan Migration & Seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Jalankan Server

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@perpustakaan.com | password |
| Member | budi@gmail.com | password |

---

## ✅ Fitur Aplikasi

### 🔐 Autentikasi
- Login dan Register
- Logout
- Role-based access (Admin & Member)
- Proteksi halaman dengan middleware

### 📖 Kelola Buku (Anggota 1 & 2)
- Daftar buku dengan pencarian dan filter kategori
- Detail buku beserta riwayat peminjaman
- Tambah buku dengan upload cover
- Edit & update data buku
- Hapus buku beserta cover-nya

### 🏷️ Kelola Kategori (Anggota 3 & 4)
- Daftar kategori dengan jumlah buku
- Detail kategori dan daftar buku di dalamnya
- Tambah, edit, hapus kategori
- Validasi: kategori tidak dapat dihapus jika masih ada buku

### 🔄 Peminjaman (Anggota 5 & 6)
- Form peminjaman dengan validasi stok
- Daftar peminjaman dengan filter status
- Detail peminjaman lengkap
- Update batas pengembalian
- Hapus data peminjaman (stok otomatis dikembalikan)
- Proses pengembalian buku dengan hitung denda otomatis
- Auto-update status jadi "Terlambat" jika melewati batas

---

## 📂 Struktur File Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       → Login, Register, Logout
│   │   ├── DashboardController.php  → Statistik dashboard
│   │   ├── BookController.php       → CRUD Buku (Anggota 1 & 2)
│   │   ├── CategoryController.php   → CRUD Kategori (Anggota 3 & 4)
│   │   └── BorrowingController.php  → CRUD Peminjaman (Anggota 5 & 6)
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php
│   ├── Member.php
│   ├── Book.php
│   ├── Category.php
│   ├── Borrowing.php
│   └── ReturnBook.php
database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_categories_table.php
│   ├── create_books_table.php
│   ├── create_members_table.php
│   ├── create_borrowings_table.php
│   └── create_returns_table.php
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── layouts/
│   └── app.blade.php               → Layout utama sidebar
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── dashboard/index.blade.php
├── books/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── categories/
│   ├── index.blade.php, create.blade.php, edit.blade.php, show.blade.php
└── borrowings/
    ├── index.blade.php, create.blade.php, edit.blade.php, show.blade.php
    └── return.blade.php
routes/
└── web.php
```

---

## 🛠️ Teknologi

- **Framework**: Laravel 10
- **Database**: MySQL
- **Frontend**: Bootstrap 5 + Bootstrap Icons
- **Auth**: Laravel built-in Auth
- **ORM**: Eloquent

---

## 📊 Kriteria Penilaian yang Terpenuhi

| Komponen | Status |
|----------|--------|
| Login, Register, Logout | ✅ |
| 3 CRUD Utama (Buku, Kategori, Peminjaman) | ✅ |
| Relasi antar tabel | ✅ |
| Migration | ✅ |
| Model & Eloquent | ✅ |
| Controller (Resource) | ✅ |
| Blade View | ✅ |
| Validasi Form | ✅ |
| CSRF pada Form | ✅ |
| Role Admin & Member | ✅ |
| Pembagian tugas C+R dan U+D | ✅ |
