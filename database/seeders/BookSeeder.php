<?php

namespace Database\Seeders;

use App\Models\Book; // Jangan lupa import model Book
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 20 data buku secara otomatis
        Book::factory()->count(20)->create();
    }
}