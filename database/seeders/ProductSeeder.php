<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set the Faker instance to Nigerian locale (en_NG)
        $faker = \Faker\Factory::create('en_NG');

        // Get all categories
        $categories = Category::all();

        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                // Generate product name
                $productName = $faker->words(3, true);

                // Generate product price
                $price = $faker->randomFloat(2, 2000, 50000);

                // Calculate discount price as a random percentage off the price (30% chance of having a discount)
                $discount_price = $faker->optional(0.3, null)->randomFloat(2, 20000, $price * 0.9); // Discount price is less than the original price

                Product::create([
                    'name' => $productName,
                    'slug' => Str::slug($productName, '-'),
                    'description' => $faker->paragraph(7),
                    'price' => $price,
                    'discount_price' => $discount_price, // Always lower than price
                    'quantity' => $faker->numberBetween(1, 20),
                    'category' => $category->name,
                    'image_one' => 'https://via.placeholder.com/400x400.png?text=Product+Image+1', // Example placeholder image
                    'image_two' => 'https://via.placeholder.com/400x400.png?text=Product+Image+2',
                    // 'rating' => $faker->numberBetween(1, 5), // Random rating between 1 and 5
                    // 'people_rated' => $faker->numberBetween(10, 100), // Random number of people who rated
                ]);
            }
        }
    }
}
