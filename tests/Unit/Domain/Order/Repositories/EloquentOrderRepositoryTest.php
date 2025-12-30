<?php

namespace Tests\Unit\Domain\Order\Repositories;

use App\Domain\Order\Repositories\EloquentOrderRepository;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentOrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentOrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentOrderRepository(new Order);
    }

    public function test_find_by_id_returns_order_with_relationships(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();

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

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-2025-ABC123',
            'total_amount' => 100,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,

        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'total_price' => 100,
        ]);

        // Act
        $result = $this->repository->findById($order->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($order->id, $result->id);
        $this->assertEquals('ORD-2025-ABC123', $result->order_number);
        $this->assertTrue($result->relationLoaded('orderItems'));
        $this->assertTrue($result->relationLoaded('user'));
        $this->assertTrue($result->orderItems->first()->relationLoaded('product'));
    }

    public function test_find_by_order_number_returns_correct_order(): void
    {
        // Arrange
        $user = User::factory()->create();

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

        $order1 = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-2025-ABC123',
            'total_amount' => 100,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-2025-XYZ789',
            'total_amount' => 100,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
        ]);

        // Act
        $result = $this->repository->findByOrderNumber('ORD-2025-ABC123');

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($order1->id, $result->id);
        $this->assertEquals('ORD-2025-ABC123', $result->order_number);
        $this->assertTrue($result->relationLoaded('orderItems'));
        $this->assertTrue($result->relationLoaded('user'));
    }

    public function test_find_orders_by_user_id_returns_paginated_user_orders(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
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

        // Create 3 orders for user1
        for ($i = 1; $i <= 3; $i++) {
            Order::factory()->create([
                'user_id' => $user1->id,
                'order_number' => "ORD-2025-USER1-{$i}",
                'created_at' => now()->subHours(1),
                'total_amount' => 100,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
            ]);
        }

        // Create 2 orders for user2
        for ($i = 1; $i <= 2; $i++) {
            Order::factory()->create([
                'user_id' => $user2->id,
                'order_number' => "ORD-2025-USER2-{$i}",
                'created_at' => now()->subHours(2),
                'total_amount' => 100,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
            ]);
        }

        // Act
        $result = $this->repository->findOrdersByUserId($user1->id, 10);

        // Assert
        $this->assertEquals(3, $result->total());
        $this->assertEquals(3, $result->count());

        foreach ($result as $order) {
            $this->assertEquals($user1->id, $order->user_id);
            $this->assertTrue($order->relationLoaded('orderItems'));
        }
    }

    public function test_find_recent_orders_returns_latest_orders_with_limit(): void
    {
        // Arrange
        $user = User::factory()->create();

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
        // Create 5 orders at different times
        $oldOrder = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-2025-OLD-001',
            'created_at' => now()->subDays(5),
            'total_amount' => 100,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
        ]);

        // Create 4 recent orders with unique order numbers
        for ($i = 1; $i <= 4; $i++) {
            Order::factory()->create([
                'user_id' => $user->id,
                'order_number' => "ORD-2025-RECENT-{$i}",
                'created_at' => now()->subHours(1),
                'total_amount' => 100,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
            ]);
        }

        // Act
        $result = $this->repository->findRecentOrders(3);

        // Assert
        $this->assertEquals(3, $result->count());

        // Verify orders are sorted by latest first (recent orders should appear first)
        $resultIds = $result->pluck('id')->toArray();
        $this->assertNotContains($oldOrder->id, $resultIds);

        foreach ($result as $order) {
            $this->assertTrue($order->relationLoaded('orderItems'));
            $this->assertTrue($order->relationLoaded('user'));
        }
    }

    public function test_create_returns_new_order(): void
    {
        // Arrange
        $user = User::factory()->create();

        $orderData = [
            'user_id' => $user->id,
            'order_number' => 'ORD-2025-TEST123',
            'status' => 'pending',
            'total_amount' => 150.00,
            'tax_amount' => 15.00,
            'shipping_amount' => 10.00,
            'shipping_address' => ['street' => '123 Test St'],
            'billing_address' => ['street' => '456 Bill Ave'],
        ];

        // Act
        $result = $this->repository->create($orderData);

        // Assert
        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('ORD-2025-TEST123', $result->order_number);
        $this->assertEquals('pending', $result->status);
        $this->assertEquals(150.00, $result->total_amount);

        // Verify it's persisted in database
        $this->assertDatabaseHas('orders', [
            'order_number' => 'ORD-2025-TEST123',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }
}
