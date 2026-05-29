# 📚 Panduan Workflow & Penjelasan Kode Program (Aplikasi Khusus Admin)

Dokumen ini berisi panduan alur kerja (*workflow*) teknis dan penjelasan cara kerja kode Laravel untuk masing-masing anggota kelompok (Anggota 1 hingga Anggota 6). Sistem telah disesuaikan agar berjalan **murni sebagai Aplikasi Khusus Admin**. Seluruh operasional (registrasi anggota, peminjaman buku, pengembalian) dilakukan sepenuhnya oleh Admin melalui dashboard internal. Hak login bagi Member dan registrasi mandiri publik telah dinonaktifkan demi keamanan.

---

## 📂 Daftar Isi
1. [Buku - Anggota 1 (C + R)](#-buku---anggota-1-c--r)
2. [Buku - Anggota 2 (U + D)](#-buku---anggota-2-u--d)
3. [Kategori - Anggota 3 (C + R)](#-kategori---anggota-3-c--r)
4. [Kategori - Anggota 4 (U + D)](#-kategori---anggota-4-u--d)
5. [Peminjaman & Pendaftaran Anggota - Anggota 5 (C + R)](#-peminjaman---anggota-5-c--r)
6. [Peminjaman & Pengembalian - Anggota 6 (U + D)](#-peminjaman---anggota-6-u--d)

---

## 📖 Buku - Anggota 1 (C + R)
**Tanggung Jawab**: Pembuatan data buku baru (*Create*) dan penampilan daftar beserta detail buku (*Read*).

### 1. R - Menampilkan Daftar Buku (Index)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengakses menu "Kelola Buku" (`/books`).
    2.  Route memanggil method `index()` di `BookController`.
    3.  Method ini mengambil data buku dari DB dengan teknik *eager loading* (`with('category')`) untuk optimasi query (menghindari masalah N+1 query).
    4.  Dilakukan pengecekan pencarian (`search`) dan filter kategori (`category_id`) dari form pencarian.
    5.  Data dipaginasi sebanyak 10 data per halaman menggunakan `paginate(10)`.
    6.  Controller mengirimkan data ke view `books.index`.
*   **Kode Rute (`routes/web.php`)**:
    ```php
    Route::resource('books', BookController::class);
    // GET /books -> BookController@index
    ```
*   **Logika Controller (`app/Http/Controllers/BookController.php`)**:
    ```php
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%')
                  ->orWhere('isbn', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }
    ```

---

### 2. C - Tambah & Simpan Buku Baru (Create & Store)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Tambah Buku", rute mengarahkan ke form tambah.
    2.  Controller mengambil semua data kategori (`Category::all()`) agar dapat dipilih di *dropdown* form. View `books.create` ditampilkan.
    3.  Admin mengisi form, mengunggah cover buku, lalu menekan tombol "Simpan".
    4.  Data dikirim melalui method POST ke `books.store`.
    5.  Controller memvalidasi semua inputan (judul wajib diisi, stok berupa angka minimal 0, cover harus file gambar maksimal 2MB, dll.).
    6.  Jika validasi lolos, file cover disimpan di direktori `public/storage/covers` menggunakan `store('covers', 'public')`.
    7.  Data buku disimpan ke tabel `books` via Eloquent `Book::create()`.
    8.  Admin dialihkan kembali ke halaman indeks disertai pesan sukses (*flash session*).
*   **Logika Controller (`app/Http/Controllers/BookController.php`)**:
    ```php
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|min:3|max:255',
            'author'      => 'required|min:3|max:255',
            'isbn'        => 'nullable|unique:books,isbn|max:20',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'publisher'   => 'nullable|max:255',
            'description' => 'nullable',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover');

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }
    ```

---

### 3. R - Detail Buku (Show)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Detail" pada baris buku tertentu.
    2.  Rute `books.show` dipanggil dengan parameter ID buku.
    3.  Laravel melakukan **Route Model Binding** secara otomatis (mencari model `Book` yang sesuai dengan ID).
    4.  Controller memuat relasi kategori beserta riwayat peminjaman buku tersebut (`load('category', 'borrowings.member.user')`).
    5.  View `books.show` dirender untuk memperlihatkan data terperinci buku beserta siapa saja yang pernah meminjamnya.
*   **Logika Controller (`app/Http/Controllers/BookController.php`)**:
    ```php
    public function show(Book $book)
    {
        $book->load('category', 'borrowings.member.user');
        return view('books.show', compact('book'));
    }
    ```

---

## 📖 Buku - Anggota 2 (U + D)
**Tanggung Jawab**: Pengubahan/pembaruan data buku (*Update*) dan penghapusan buku beserta berkas cover (*Delete*).

### 1. U - Edit & Update Buku
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Edit" pada buku tertentu.
    2.  Rute `books.edit` memanggil method `edit(Book $book)`.
    3.  Controller mengirim data objek `$book` dan daftar kategori ke view `books.edit`.
    4.  Admin mengubah data di form (misal mengganti jumlah stok atau mengganti cover baru) dan mengklik "Update".
    5.  Request dikirim dengan metode PUT/PATCH ke `books.update`.
    6.  Controller melakukan validasi. Khusus untuk unik ISBN, ditambahkan pengecualian untuk ID buku itu sendiri (`unique:books,isbn,' . $book->id)`.
    7.  Jika ada cover baru yang diunggah:
        *   Controller menghapus cover lama dari storage server (`Storage::disk('public')->delete($book->cover)`) untuk menghemat ruang penyimpanan.
        *   Menyimpan file cover yang baru ke storage.
    8.  Objek buku diperbarui dengan perintah `$book->update($data)`.
    9.  Admin dialihkan kembali ke indeks dengan pesan sukses.
*   **Logika Controller (`app/Http/Controllers/BookController.php`)**:
    ```php
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'       => 'required|min:3|max:255',
            'author'      => 'required|min:3|max:255',
            'isbn'        => 'nullable|unique:books,isbn,' . $book->id . '|max:20',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'publisher'   => 'nullable|max:255',
            'description' => 'nullable',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover');

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui!');
    }
    ```

---

### 2. D - Menghapus Buku (Destroy)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin menekan tombol "Hapus" pada buku tertentu.
    2.  Halaman mengirim request bertipe DELETE ke rute `books.destroy`.
    3.  Sebelum data buku dihapus dari database, controller memeriksa apakah buku memiliki berkas cover. Jika ada, file cover tersebut dihapus dari disk penyimpanan server.
    4.  Data buku dihapus secara permanen dari tabel menggunakan `$book->delete()`.
    5.  Admin diredirect ke daftar buku dengan notifikasi sukses.
*   **Logika Controller (`app/Http/Controllers/BookController.php`)**:
    ```php
    public function destroy(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
    }
    ```

---

## 🏷️ Kategori - Anggota 3 (C + R)
**Tanggung Jawab**: Penambahan kategori baru (*Create*) dan menampilkan daftar beserta detail kategori (*Read*).

### 1. R - Menampilkan Daftar Kategori
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin membuka menu "Kategori" (`/categories`).
    2.  Controller menjalankan query `Category::withCount('books')` untuk menghitung jumlah buku per kategori secara optimal.
    3.  Mendukung fitur pencarian nama kategori.
    4.  Hasil dikirimkan ke view `categories.index`.
*   **Logika Controller (`app/Http/Controllers/CategoryController.php`)**:
    ```php
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }
    ```

---

### 2. C - Tambah & Simpan Kategori
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik "Tambah Kategori".
    2.  View `categories.create` menyajikan form nama kategori dan deskripsi singkat.
    3.  Setelah disubmit (POST), controller memvalidasi keunikan nama kategori (`unique:categories,name`).
    4.  Kategori disimpan menggunakan Eloquent Mass Assignment `Category::create()`.
    5.  Kembali ke indeks kategori dengan pesan sukses.
*   **Logika Controller (`app/Http/Controllers/CategoryController.php`)**:
    ```php
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|min:2|max:100|unique:categories,name',
            'description' => 'nullable|max:500',
        ]);

        Category::create($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }
    ```

---

### 3. R - Detail Kategori (Show)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik detail kategori tertentu.
    2.  Controller memanggil fungsi `show(Category $category)`.
    3.  Mengambil semua buku yang berelasi dengan kategori tersebut secara terpilih dengan paginasi: `$category->books()->paginate(10)`.
    4.  View `categories.show` menampilkan deskripsi kategori dan tabel buku yang masuk dalam kategori ini.
*   **Logika Controller (`app/Http/Controllers/CategoryController.php`)**:
    ```php
    public function show(Category $category)
    {
        $books = $category->books()->paginate(10);
        return view('categories.show', compact('category', 'books'));
    }
    ```

---

## 🏷️ Kategori - Anggota 4 (U + D)
**Tanggung Jawab**: Pengeditan data kategori (*Update*) dan penghapusan aman (*Delete*) kategori.

### 1. U - Edit & Update Kategori
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik "Edit" kategori, rute membawa ke view `categories.edit` berisi data lama.
    2.  Setelah disunting dan disubmit (PUT), data divalidasi.
    3.  Pengecekan keunikan nama dilewati jika nama tidak diubah (`unique:categories,name,' . $category->id).
    4.  Kategori diperbarui via `$category->update()`.
*   **Logika Controller (`app/Http/Controllers/CategoryController.php`)**:
    ```php
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|min:2|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:500',
        ]);

        $category->update($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }
    ```

---

### 2. D - Menghapus Kategori Aman (Destroy)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik "Hapus" kategori.
    2.  **Validasi Integritas Data**: Sebelum menghapus, controller melakukan pengecekan apakah kategori ini masih digunakan oleh buku manapun dengan `$category->books()->count() > 0`.
    3.  Jika **ya**, penghapusan dibatalkan dan memunculkan *flash message error* "Kategori tidak dapat dihapus karena masih memiliki buku!". Ini menjaga agar relasi database tidak rusak (*broken constraint*).
    4.  Jika **tidak**, data kategori dihapus dengan aman melalui `$category->delete()`.
*   **Logika Controller (`app/Http/Controllers/CategoryController.php`)**:
    ```php
    public function destroy(Category $category)
    {
        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki buku!');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
    ```

---

## 🔄 Peminjaman & Pendaftaran Anggota - Anggota 5 (C + R)
**Tanggung Jawab**: Pembuatan data peminjaman buku baru (*Create*), pendaftaran anggota secara manual oleh Admin, dan penampilan daftar peminjaman beserta detail (*Read*).

### 1. R - Menampilkan Daftar Transaksi Peminjaman
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin membuka menu "Peminjaman" (`/borrowings`).
    2.  Controller memuat data peminjaman beserta relasi user anggota (`member.user`) dan data buku (`book`).
    3.  **Logika Otomatisasi Overdue**: Sebelum data dirender, controller secara otomatis memindai transaksi berstatus `'borrowed'` yang tanggal pengembaliannya (`due_date`) telah melewati hari ini (`due_date < now()`), lalu memperbarui statusnya menjadi `'overdue'` secara massal di database.
    4.  Mendukung pencarian nama anggota atau judul buku, serta filter berdasarkan status peminjaman.
    5.  Merender view `borrowings.index` dengan data paginasi.
*   **Logika Controller (`app/Http/Controllers/BorrowingController.php`)**:
    ```php
    public function index(Request $request)
    {
        $query = Borrowing::with(['member.user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('member.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('book', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        // Auto-update status overdue
        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $borrowings = $query->latest()->paginate(10);

        return view('borrowings.index', compact('borrowings'));
    }
    ```

---

### 2. C - Pendaftaran Anggota Baru secara Manual oleh Admin
Karena pendaftaran mandiri dinonaktifkan, Admin bertanggung jawab mendaftarkan anggota baru.
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "**Tambah Anggota**" di halaman `/admin/members`.
    2.  Controller `AdminMemberController@create` menyajikan form isian data diri anggota (Nama, Email, Telepon, Tanggal Lahir, Alamat).
    3.  Saat form disubmit (POST), controller memvalidasi keunikan email anggota.
    4.  Sistem membuat record `User` baru dengan role `member` dan password acak di belakang layar.
    5.  Sistem menghitung id member terakhir dan menghasilkan kode anggota berurutan format `MBR-XXXXX` secara otomatis.
    6.  Record `Member` baru dibuat di database.
*   **Logika Controller (`app/Http/Controllers/AdminMemberController.php`)**:
    ```php
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|min:3|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|max:20',
            'birth_date' => 'nullable|date',
            'address'    => 'nullable|max:500',
        ]);

        // Buat User dengan role member dan password acak
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            'role'     => 'member',
        ]);

        // Generate kode member berurutan MBR-00001 dst
        $lastMemberId = Member::max('id') ?? 0;
        $nextId = $lastMemberId + 1;
        $memberCode = 'MBR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        Member::create([
            'user_id'     => $user->id,
            'member_code' => $memberCode,
            'phone'       => $request->phone,
            'birth_date'  => $request->birth_date,
            'address'     => $request->address,
            'status'      => 'active',
        ]);

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota baru berhasil didaftarkan dengan kode ' . $memberCode . '!');
    }
    ```

---

### 3. C - Pembuatan Transaksi Peminjaman Baru oleh Admin
*   **Aliran Kerja Peminjaman (*Workflow*)**:
    1.  Admin mengklik "**Tambah Peminjaman**" di halaman `/borrowings`.
    2.  Form `borrowings.create` menampilkan pilihan Anggota Aktif dan pilihan Buku yang stoknya masih lebih dari nol (`stock > 0`).
    3.  Admin mengisi data termasuk Tanggal Pinjam (`borrow_date`) dan Batas Pengembalian (`due_date`).
    4.  Saat disubmit, controller memvalidasi input dan memeriksa stok buku.
    5.  Jika stok tersedia, stok buku dikurangi satu (`$book->decrement('stock')`).
    6.  Peminjaman dicatat langsung dengan status `'borrowed'` (karena Admin menginput langsung secara fisik).
*   **Logika Controller (`app/Http/Controllers/BorrowingController.php`)**:
    ```php
    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'book_id'     => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date'    => 'required|date|after:borrow_date',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku tidak tersedia!')->withInput();
        }

        $book->decrement('stock');

        Borrowing::create([
            'member_id'   => $request->member_id,
            'book_id'     => $request->book_id,
            'borrow_date' => $request->borrow_date,
            'due_date'    => $request->due_date,
            'status'      => 'borrowed',
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman berhasil dicatat!');
    }
    ```

---

## 🔄 Peminjaman & Pengembalian - Anggota 6 (U + D)
**Tanggung Jawab**: Perpanjangan/pengeditan peminjaman (*Update*), penghapusan transaksi (*Delete*), dan pemrosesan Pengembalian Buku + Kalkulasi Denda otomatis.

### 1. U - Edit/Perpanjang Peminjaman
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Edit" pada transaksi aktif.
    2.  Controller memastikan status transaksi bukan `'returned'`.
    3.  Admin memperpanjang tanggal kembali (`due_date`) atau menyesuaikan status.
    4.  Data diupdate melalui `$borrowing->update()`.
*   **Logika Controller (`app/Http/Controllers/BorrowingController.php`)**:
    ```php
    public function update(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'due_date' => 'required|date|after:borrow_date',
            'status'   => 'required|in:borrowed,overdue',
        ]);

        $borrowing->update([
            'due_date' => $request->due_date,
            'status'   => $request->status,
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil diperbarui!');
    }
    ```

---

### 2. U - Memproses Pengembalian Buku & Hitung Denda Otomatis
Ini merupakan sub-fitur kritikal dari bagian Update (U) Peminjaman:
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Kembalikan Buku" di dashboard.
    2.  Admin mengisi Tanggal Pengembalian Riil (`return_date`).
    3.  Saat disubmit, controller menghitung selisih hari antara Tanggal Pengembalian (`return_date`) dengan Batas Pengembalian (`due_date`) menggunakan library **Carbon** (`diffInDays`).
    4.  **Logika Kalkulasi Denda**: Jika terlambat, denda dihitung otomatis dengan tarif **Rp 1.000 per hari**. Jika tidak, denda diisi `0`.
    5.  Merekam data pengembalian baru ke tabel `returns`.
    6.  Memperbarui status peminjaman menjadi `'returned'`.
    7.  **Mengembalikan Stok Buku**: Menambahkan kembali stok buku sebanyak 1 unit (`$borrowing->book->increment('stock')`).
*   **Logika Controller (`app/Http/Controllers/BorrowingController.php`)**:
    ```php
    public function returnBook(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'return_date' => 'required|date|after_or_equal:' . $borrowing->borrow_date,
            'notes'       => 'nullable|max:500',
        ]);

        $returnDate = Carbon::parse($request->return_date);
        $dueDate    = Carbon::parse($borrowing->due_date);

        $fine = 0;
        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $fine     = $daysLate * 1000;
        }

        ReturnBook::create([
            'borrowing_id' => $borrowing->id,
            'return_date'  => $request->return_date,
            'fine'         => $fine,
            'notes'        => $request->notes,
        ]);

        $borrowing->update(['status' => 'returned']);
        $borrowing->book->increment('stock');

        return redirect()->route('borrowings.index')
            ->with('success', 'Buku berhasil dikembalikan!' . ($fine > 0 ? ' Denda: Rp ' . number_format($fine, 0, ',', '.') : ''));
    }
    ```

---

### 3. D - Menghapus Peminjaman (Destroy)
*   **Aliran Kerja (*Workflow*)**:
    1.  Admin mengklik tombol "Hapus" pada record peminjaman.
    2.  **Penanganan Stok Aman**: Sebelum record dihapus, jika statusnya masih aktif (`borrowed` atau `overdue`), sistem secara otomatis mengembalikan stok buku di database (`$borrowing->book->increment('stock')`) terlebih dahulu agar data stok buku tetap sinkron.
    3.  Data transaksi dihapus dari database.
*   **Logika Controller (`app/Http/Controllers/BorrowingController.php`)**:
    ```php
    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status === 'borrowed' || $borrowing->status === 'overdue') {
            $borrowing->book->increment('stock');
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil dihapus!');
    }
    ```
