<?php

namespace Database\Factories;

use App\Models\SharedDocument;
use App\Models\SharedType; // Pastikan model ini di-import
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SharedDocumentFactory extends Factory
{
    protected $model = SharedDocument::class;

    public function definition()
    {
        $title = $this->faker->words(4, true);
        $timestamp = now()->timestamp;
        
        // Buat tanggal acak dari 5 tahun yang lalu sampai hari ini
        $createdAt = $this->faker->dateTimeBetween('-5 years', 'now');
        // Pastikan tanggal update terjadi setelah tanggal pembuatan
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');

        return [
            'category_id' => SharedType::inRandomOrder()->value('id'),
            'title' => ucwords($title),
            'description' => $this->faker->sentence(),
            'file_path' => "shared_documents/{$timestamp}_" . \Illuminate\Support\Str::slug($title) . ".pdf",
            'division_id' => $this->faker->numberBetween(1, 5),
            'user_id' => $this->faker->randomElement([1, 4]), 
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}