<?php

namespace Tests\Unit\Domain\Order\Services;

use App\Domain\Order\Services\OrderService;
use App\Jobs\CheckLowStockNotification;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderService;
        Queue::fake();
    }

    public function test_place_order_creates_order_successfully(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $shippingAddress = [
            'street' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
        ];

        $billingAddress = [
            'street' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip' => '90001',
        ];

        // Act
        $order = $this->service->placeOrder($user, $shippingAddress, $billingAddress);

        // Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(230.00, $order->total_amount); // 200 + 20 tax + 10 shipping
        $this->assertEquals(20.00, $order->tax_amount);
        $this->assertEquals(10.00, $order->shipping_amount);
        $this->assertEquals($shippingAddress, $order->shipping_address);
        $this->assertEquals($billingAddress, $order->billing_address);
        $this->assertStringContainsString('ORD-', $order->order_number);

        // Verify order items created
        $this->assertEquals(1, $order->orderItems->count());
        $orderItem = $order->orderItems->first();
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(100.00, $orderItem->unit_price);
        $this->assertEquals(200.00, $orderItem->total_price);

        // Verify stock updated
        $product->refresh();
        $this->assertEquals(8, $product->stock_quantity);

        // Verify cart cleared
        $this->assertEquals(0, $user->cartItems()->count());
    }

    public function test_place_order_throws_exception_when_insufficient_stock(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'stock_quantity' => 1,
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $shippingAddress = ['street' => '123 Main St'];
        $billingAddress = ['street' => '456 Oak Ave'];

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock for Test Product');

        // Act
        $this->service->placeOrder($user, $shippingAddress, $billingAddress);
    }

    public function test_place_order_dispatches_low_stock_notification_job(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 7, // Will become 2 after order (below threshold of 5)
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $shippingAddress = ['street' => '123 Main St'];
        $billingAddress = ['street' => '456 Oak Ave'];

        // Act
        $this->service->placeOrder($user, $shippingAddress, $billingAddress);

        // Assert
        Queue::assertPushed(CheckLowStockNotification::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });

        // Verify stock is indeed low
        $product->refresh();
        $this->assertEquals(2, $product->stock_quantity);
    }

    public function test_place_order_does_not_dispatch_notification_when_stock_sufficient(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 10, // Will become 8 after order (above threshold of 5)
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $shippingAddress = ['street' => '123 Main St'];
        $billingAddress = ['street' => '456 Oak Ave'];

        // Act
        $this->service->placeOrder($user, $shippingAddress, $billingAddress);

        // Assert
        Queue::assertNotPushed(CheckLowStockNotification::class);

        // Verify stock is still sufficient
        $product->refresh();
        $this->assertEquals(8, $product->stock_quantity);
    }

    public function test_place_order_calculates_totals_correctly(): void
    {
        // Arrange
        $user = User::factory()->create();

        $product1 = Product::factory()->create(['price' => 25.00, 'stock_quantity' => 10]);
        $product2 = Product::factory()->create(['price' => 75.00, 'stock_quantity' => 10]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 2, // 2 x 25 = 50
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'quantity' => 1, // 1 x 75 = 75
        ]);

        $shippingAddress = ['street' => '123 Main St'];
        $billingAddress = ['street' => '456 Oak Ave'];

        // Act
        $order = $this->service->placeOrder($user, $shippingAddress, $billingAddress);

        // Assert
        // Subtotal: 50 + 75 = 125
        // Tax: 125 * 0.1 = 12.5
        // Shipping: 10
        // Total: 125 + 12.5 + 10 = 147.5
        $this->assertEquals(147.50, $order->total_amount);
        $this->assertEquals(12.50, $order->tax_amount);
        $this->assertEquals(10.00, $order->shipping_amount);

        // Verify order items
        $this->assertEquals(2, $order->orderItems->count());
    }

    public function test_place_order_handles_transaction_rollback_on_error(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Create a product that will cause stock validation to fail
        $product = Product::factory()->create([
            'name' => 'Limited Product',
            'price' => 100.00,
            'stock_quantity' => 1,
        ]);

        // Create cart item with quantity greater than available stock
        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5, // More than stock_quantity of 1
        ]);

        $shippingAddress = ['street' => '123 Main St'];
        $billingAddress = ['street' => '456 Oak Ave'];

        // Record initial state
        $initialOrderCount = Order::count();
        $initialOrderItemCount = OrderItem::count();
        $initialCartCount = $user->cartItems()->count();
        $initialStock = $product->stock_quantity;

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock for Limited Product');

        try {
            $this->service->placeOrder($user, $shippingAddress, $billingAddress);
        } finally {
            // Verify transaction rollback worked - state should be unchanged
            $this->assertEquals($initialOrderCount, Order::count());
            $this->assertEquals($initialOrderItemCount, OrderItem::count());
            $this->assertEquals($initialCartCount, $user->cartItems()->count()); // Cart not cleared
            $product->refresh();
            $this->assertEquals($initialStock, $product->stock_quantity); // Stock not updated
        }
    }
}
