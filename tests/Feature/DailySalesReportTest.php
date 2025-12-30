<?php

namespace Tests\Feature;

use App\Jobs\SendDailySalesReport;
use App\Mail\DailySalesReport;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_sales_report_email_is_sent_with_sales_data(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'shawki.course@gmail.com',
            'name' => 'Admin User',
        ]);

        // Create users, categories, and products
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::factory()->create();
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 25.99,
            'name' => 'Test Product 1',
            'sku' => 'TEST-001',
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 15.50,
            'name' => 'Test Product 2',
            'sku' => 'TEST-002',
        ]);

        // Create orders for yesterday
        $yesterday = Carbon::yesterday();
        $order1 = Order::factory()->create([
            'user_id' => $user1->id,
            'total_amount' => 77.97,
            'created_at' => $yesterday,
        ]);
        $order2 = Order::factory()->create([
            'user_id' => $user2->id,
            'total_amount' => 31.00,
            'created_at' => $yesterday,
        ]);

        // Create order items
        $order1->orderItems()->create([
            'product_id' => $product1->id,
            'quantity' => 3,
            'unit_price' => 25.99,
            'total_price' => 77.97,
        ]);
        $order2->orderItems()->create([
            'product_id' => $product2->id,
            'quantity' => 2,
            'unit_price' => 15.50,
            'total_price' => 31.00,
        ]);

        // Execute the job for yesterday
        $job = new SendDailySalesReport($yesterday->toDateString());
        $job->handle();

        // Assert email was sent
        Mail::assertSent(DailySalesReport::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email) &&
                   $mail->salesData['total_revenue'] == 108.97 &&
                   $mail->salesData['total_items_sold'] == 5 &&
                   count($mail->salesData['orders']) == 2 &&
                   count($mail->salesData['products']) == 2;
        });
    }

    public function test_daily_sales_report_email_is_sent_with_no_sales(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'shawki.course@gmail.com',
            'name' => 'Admin User',
        ]);

        // Execute the job for a date with no sales
        $yesterday = Carbon::yesterday();
        $job = new SendDailySalesReport($yesterday->toDateString());
        $job->handle();

        // Assert email was sent with empty data
        Mail::assertSent(DailySalesReport::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email) &&
                   $mail->salesData['total_revenue'] == 0 &&
                   $mail->salesData['total_items_sold'] == 0 &&
                   count($mail->salesData['orders']) == 0 &&
                   count($mail->salesData['products']) == 0;
        });
    }

    public function test_daily_sales_report_calculates_correct_statistics(): void
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'shawki.course@gmail.com',
            'name' => 'Admin User',
        ]);

        // Create test data
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 20.00,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
        ]);

        // Create multiple orders for yesterday
        $yesterday = Carbon::yesterday();
        $order1 = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 40.00,
            'created_at' => $yesterday,
        ]);
        $order2 = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 60.00,
            'created_at' => $yesterday,
        ]);

        // Create order items
        $order1->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 20.00,
            'total_price' => 40.00,
        ]);
        $order2->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 20.00,
            'total_price' => 60.00,
        ]);

        // Execute the job
        $job = new SendDailySalesReport($yesterday->toDateString());
        $job->handle();

        // Assert statistics are calculated correctly
        Mail::assertSent(DailySalesReport::class, function ($mail) use ($admin) {
            $salesData = $mail->salesData;

            return $mail->hasTo($admin->email) &&
                   $salesData['total_revenue'] == 100.00 &&
                   $salesData['total_items_sold'] == 5 &&
                   $salesData['average_order_value'] == 50.00 &&
                   count($salesData['orders']) == 2 &&
                   count($salesData['products']) == 1 &&
                   $salesData['products'][0]['quantity_sold'] == 5;
        });
    }
}
