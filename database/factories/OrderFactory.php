<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = $product->selling_price;
        $totalAmount = $unitPrice * $quantity;

        return [
            'order_number' => 'GTU' . strtoupper(uniqid()),
            'user_id' => fake()->boolean(70) ? User::factory() : null,
            'product_id' => $product->id,
            'customer_email' => fake()->email(),
            'customer_whatsapp' => fake()->phoneNumber(),
            'game_id' => fake()->numerify('############'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'payment_method' => fake()->randomElement(['QRIS', 'DANA', 'GOPAY', 'OVO', 'ALFAMART', 'INDOMARET']),
            'payment_reference' => 'TXN' . fake()->numerify('##########'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }
}