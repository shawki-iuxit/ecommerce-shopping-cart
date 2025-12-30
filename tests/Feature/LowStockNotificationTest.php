<?php

namespace Tests\Feature;

use App\Jobs\CheckLowStockNotification;
use App\Mail\LowStockNotification;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LowStockNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_job_is_dispatched_after_order(): void
    {
        Queue::fake();
        
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        // Create a regular user
        $user = User::factory()->create();

        // Create a category and product with low stock
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock_quantity' => 10,
            'price' => 19.99,
        ]);

        // Add product to cart
        $user->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 8, // This will leave 2 in stock (below threshold of 5)
        ]);

        // Place order
        $orderService = app(\App\Domain\Order\Services\OrderService::class);
        $orderService->placeOrder($user, [], []);

        // Assert job was dispatched
        Queue::assertPushed(CheckLowStockNotification::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });
    }

    public function test_low_stock_notification_email_is_sent_when_stock_is_low(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        // Create a product with low stock
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock_quantity' => 3, // Below threshold
            'price' => 29.99,
        ]);

        // Execute the job
        $job = new CheckLowStockNotification($product);
        $job->handle();

        // Assert email was sent
        Mail::assertSent(LowStockNotification::class, function ($mail) use ($admin, $product) {
            return $mail->hasTo($admin->email) && 
                   $mail->product->id === $product->id;
        });
    }

    public function test_low_stock_notification_email_is_not_sent_when_stock_is_sufficient(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        // Create a product with sufficient stock
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock_quantity' => 10, // Above threshold
            'price' => 29.99,
        ]);

        // Execute the job
        $job = new CheckLowStockNotification($product);
        $job->handle();

        // Assert no email was sent
        Mail::assertNothingSent();
    }
}
