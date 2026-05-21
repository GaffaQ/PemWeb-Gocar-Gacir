<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3), // Mengisi judul buku
            'author' => fake()->name(), // Nama pengarang acak
            'isbn' => fake()->isbn13(), // Nomor ISBN acak
            // Mengambil ID kategori secara acak dari database
            'category_id' => Category::inRandomOrder()->first()->id ?? 1, 
            'stock' => fake()->numberBetween(1, 50),
            'year' => fake()->year(),
            'publisher' => fake()->company(), // Nama perusahaan acak untuk penerbit
            'description' => fake()->paragraph(), // Paragraf acak untuk deskripsi
            'cover' => null, // Kita kosongkan dulu karena ini biasanya upload file gambar
        ];
    }
}