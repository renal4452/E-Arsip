<?php

namespace Database\Factories;

use App\Models\DocVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocVersionFactory extends Factory
{
    protected $model = DocVersion::class;

    public function definition()
    {
        $timestamp = now()->timestamp;
        $randomFileName = Str::slug($this->faker->words(3, true));
        $version = 1;

        return [
            'doc_id' => null, // Akan diisi otomatis oleh DocumentFactory
            'version_number' => $version,
            'file_path' => "documents/v{$version}_{$timestamp}_{$randomFileName}.pdf",
            'file_size' => $this->faker->numberBetween(10240, 2048000),
            'uploaded_by' => $this->faker->randomElement([1, 4]), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}