<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $productsByCategory = [
            'Electronics' => [
                ['name' => 'iPhone 15 Pro', 'sku' => 'IPH15PRO001', 'price' => 999.99, 'stock_quantity' => 50, 'description' => 'Latest iPhone with advanced features and stunning design.', 'image_url' => 'images/iphone16.jpg'],
                ['name' => 'MacBook Air M2', 'sku' => 'MBA13M2001', 'price' => 1199.99, 'stock_quantity' => 30, 'description' => 'Lightweight laptop with M2 chip for incredible performance.', 'image_url' => 'images/macbook.jpeg'],
                ['name' => 'AirPods Pro', 'sku' => 'APDPRO001', 'price' => 249.99, 'stock_quantity' => 75, 'description' => 'Active noise cancellation wireless earbuds.', 'image_url' => null],
                ['name' => 'iPad Air', 'sku' => 'IPDAIR001', 'price' => 599.99, 'stock_quantity' => 40, 'description' => 'Versatile tablet for work and creativity.', 'image_url' => null],
                ['name' => 'Apple Watch Series 9', 'sku' => 'AWS9001', 'price' => 399.99, 'stock_quantity' => 60, 'description' => 'Advanced smartwatch with health monitoring.', 'image_url' => null],
            ],
            'Clothing' => [
                ['name' => 'Classic Denim Jacket', 'sku' => 'CDJ001', 'price' => 79.99, 'stock_quantity' => 25, 'description' => 'Timeless denim jacket perfect for any season.', 'image_url' => 'images/jacket.jpg'],
                ['name' => 'Cotton T-Shirt', 'sku' => 'CTS001', 'price' => 19.99, 'stock_quantity' => 100, 'description' => 'Comfortable 100% cotton t-shirt in multiple colors.', 'image_url' => 'images/jacket.jpg'],
                ['name' => 'Running Sneakers', 'sku' => 'RNS001', 'price' => 129.99, 'stock_quantity' => 45, 'description' => 'High-performance running shoes with superior comfort.', 'image_url' => 'images/jacket.jpg'],
            ],
            'Books' => [
                ['name' => 'The Great Gatsby', 'sku' => 'TGG001', 'price' => 12.99, 'stock_quantity' => 80, 'description' => 'Classic American novel by F. Scott Fitzgerald.', 'image_url' => 'images/book.jpg'],
                ['name' => 'Programming Fundamentals', 'sku' => 'PF001', 'price' => 45.99, 'stock_quantity' => 25, 'description' => 'Comprehensive guide to programming concepts.', 'image_url' => 'images/book.jpg'],
            ],
            'Sports' => [
                ['name' => 'Basketball', 'sku' => 'BB001', 'price' => 29.99, 'stock_quantity' => 50, 'description' => 'Professional grade basketball for outdoor play.', 'image_url' => 'images/basketball.webp'],
                ['name' => 'Tennis Racket', 'sku' => 'TR001', 'price' => 129.99, 'stock_quantity' => 25, 'description' => 'High-quality tennis racket for competitive play.', 'image_url' => 'images/tennis.jpg'],
                ['name' => 'Dumbbells Set', 'sku' => 'DS001', 'price' => 199.99, 'stock_quantity' => 15, 'description' => 'Adjustable dumbbells for home gym workouts.', 'image_url' => null],
                ['name' => 'Soccer Ball', 'sku' => 'SB001', 'price' => 24.99, 'stock_quantity' => 70, 'description' => 'Official size soccer ball for training and games.', 'image_url' => null],
            ],
        ];

        foreach ($categories as $category) {
            if (isset($productsByCategory[$category->name])) {
                foreach ($productsByCategory[$category->name] as $productData) {
                    Product::create([
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'sku' => $productData['sku'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock_quantity' => $productData['stock_quantity'],
                        'image_url' => $productData['image_url'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
