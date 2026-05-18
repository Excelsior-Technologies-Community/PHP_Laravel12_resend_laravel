<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Order; 

class OrderFactory extends Factory
{
    protected $model = Order::class; 
    public function definition(): array
    {
        return [
            'order_no' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->unique()->safeEmail(),
            'product_name' => fake()->randomElement([
                'MacBook Pro M3 Max', 
                'iPhone 16 Pro Max', 
                'iPad Pro Ultra', 
                'AirPods Max'
            ]),
            'price' => fake()->randomFloat(2, 50000, 250000),
        ];
    }
}