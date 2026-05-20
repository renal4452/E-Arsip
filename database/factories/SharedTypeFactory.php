<?php

namespace Database\Factories;

use App\Models\SharedType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SharedTypeFactory extends Factory
{
    protected $model = SharedType::class;

    public function definition()
    {
        return [
            'name' => 'Kategori ' . $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}