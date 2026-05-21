<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user Admin
        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@perpustakaan.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

	$this->call([
        CategorySeeder::class, // Kategori harus di atas!
        BookSeeder::class,     // Buku menyusul di bawahnya
        ]);

        // Buat anggota biasa
        $member1 = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);
        Member::create(['user_id' => $member1->id, 'member_code' => 'MBR-00001', 'phone' => '08123456789', 'status' => 'active']);

        $member2 = User::create([
            'name'     => 'Siti Rahayu',
            'email'    => 'siti@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);
        Member::create(['user_id' => $member2->id, 'member_code' => 'MBR-00002', 'phone' => '08234567890', 'status' => 'active']);

        // Buat kategori
        $categories = [
            ['name' => 'Fiksi', 'description' => 'Novel, cerpen, dan karya fiksi lainnya'],
            ['name' => 'Sains & Teknologi', 'description' => 'Buku ilmu pengetahuan dan teknologi'],
            ['name' => 'Sejarah', 'description' => 'Buku-buku sejarah Indonesia dan dunia'],
            ['name' => 'Pendidikan', 'description' => 'Buku teks dan referensi pendidikan'],
            ['name' => 'Agama', 'description' => 'Buku-buku keagamaan'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Buat buku contoh
        $books = [
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'category_id' => 1, 'stock' => 5, 'year' => 2005, 'publisher' => 'Bentang Pustaka', 'isbn' => '978-979-1227-57-1'],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'category_id' => 1, 'stock' => 3, 'year' => 1980, 'publisher' => 'Hasta Mitra'],
            ['title' => 'Sapiens: Sejarah Singkat Umat Manusia', 'author' => 'Yuval Noah Harari', 'category_id' => 3, 'stock' => 2, 'year' => 2011, 'publisher' => 'KPG'],
            ['title' => 'Pemrograman Laravel', 'author' => 'Samsul Arifin', 'category_id' => 2, 'stock' => 4, 'year' => 2022, 'publisher' => 'Elex Media'],
            ['title' => 'Algoritma dan Pemrograman', 'author' => 'Rinaldi Munir', 'category_id' => 2, 'stock' => 6, 'year' => 2019, 'publisher' => 'Informatika'],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        echo "Seeder berhasil!\n";
        echo "Admin: admin@perpustakaan.com / password\n";
        echo "Member: budi@gmail.com / password\n";
    }
}
