<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();

        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create([
                'name' => 'Sample Product ' . $i,
                'slug' => 'sample-product-' . $i,
                'description' => 'Test description for product ' . $i,
                'price' => rand(10, 100),
                'stock_quantity' => rand(5, 50),
                // 'images' => ['products/sample.jpg'], // make sure sample.jpg exists in storage/app/public/products/
            ]);

            // Attach 1–2 random categories
            $product->categories()->attach($categories->random(rand(1, 2))->pluck('id'));
        }
    }
}
