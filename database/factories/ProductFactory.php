<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basePrice = fake()->randomFloat(2, 5000, 500000);
        $profitPercentage = fake()->randomFloat(2, 10, 30);
        $sellingPrice = $basePrice * (1 + $profitPercentage / 100);
        
        $name = fake()->randomElement([
            '5 Diamonds',
            '12 Diamonds', 
            '28 Diamonds',
            '56 Diamonds',
            '112 Diamonds',
            '224 Diamonds',
            '448 Diamonds',
            '875 Diamonds',
            '1688 Diamonds',
            '3688 Diamonds',
            '7688 Diamonds',
            'Weekly Diamond Pass',
            'Monthly Diamond Pass',
            'Battle Pass',
            'Starlight Member',
            'Elite Pass',
            'Royal Pass',
            'Prime Pass',
            'UC 60',
            'UC 120',
            'UC 300',
            'UC 600',
            'UC 1500',
            'UC 3000',
        ]);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name . '-' . fake()->unique()->numberBetween(1, 1000)),
            'sku' => 'SKU' . fake()->unique()->numerify('######'),
            'description' => fake()->paragraph(),
            'base_price' => $basePrice,
            'selling_price' => $sellingPrice,
            'profit_percentage' => $profitPercentage,
            'is_active' => fake()->boolean(90),
            'is_flash_sale' => fake()->boolean(20),
            'flash_sale_price' => fake()->boolean(20) ? $sellingPrice * 0.8 : null,
            'flash_sale_start' => fake()->boolean(20) ? now()->subDays(fake()->numberBetween(1, 5)) : null,
            'flash_sale_end' => fake()->boolean(20) ? now()->addDays(fake()->numberBetween(1, 10)) : null,
            'sort_order' => fake()->numberBetween(0, 100),
            'digiflazz_code' => 'DG' . fake()->numerify('####'),
            'requires_game_id' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the product is on flash sale.
     */
    public function flashSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_flash_sale' => true,
            'flash_sale_price' => $attributes['selling_price'] * 0.8,
            'flash_sale_start' => now()->subDay(),
            'flash_sale_end' => now()->addDays(3),
        ]);
    }
}