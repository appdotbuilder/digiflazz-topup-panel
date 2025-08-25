<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Mobile Legends',
            'Free Fire',
            'PUBG Mobile',
            'Genshin Impact',
            'Call of Duty Mobile',
            'Arena of Valor',
            'Valorant',
            'League of Legends',
            'Clash of Clans',
            'Clash Royale'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(10),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}