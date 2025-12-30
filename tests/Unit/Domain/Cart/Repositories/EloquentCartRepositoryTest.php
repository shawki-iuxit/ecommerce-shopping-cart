<?php

namespace Tests\Unit\Domain\Cart\Repositories;

use App\Domain\Cart\Repositories\EloquentCartRepository;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentCartRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentCartRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentCartRepository(new CartItem);
    }

    public function test_get_cart_items_by_user_id_returns_items_with_relationships(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'user_id' => $user1->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'user_id' => $user2->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Act
        $result = $this->repository->getCartItemsByUserId($user1->id);

        // Assert
        $this->assertEquals(1, $result->count());
        $cartItem = $result->first();
        $this->assertEquals($user1->id, $cartItem->user_id);
        $this->assertTrue($cartItem->relationLoaded('product'));
        $this->assertTrue($cartItem->product->relationLoaded('category'));
    }

    public function test_find_cart_item_returns_item_when_exists(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Act
        $result = $this->repository->findCartItem($user->id, $product->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($cartItem->id, $result->id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
    }

    public function test_add_item_to_cart_creates_new_item_when_not_exists(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Act
        $result = $this->repository->addItemToCart($user->id, $product->id, 2);

        // Assert
        $this->assertInstanceOf(CartItem::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(2, $result->quantity);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_update_cart_item_quantity_updates_existing_item(): void
    {
        // Arrange
        $cartItem = CartItem::factory()->create(['quantity' => 2]);

        // Act
        $result = $this->repository->updateCartItemQuantity($cartItem->id, 5);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_remove_cart_item_deletes_item(): void
    {
        // Arrange
        $cartItem = CartItem::factory()->create();

        // Act
        $result = $this->repository->removeCartItem($cartItem->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
            'deleted_at' => null,
        ]);
    }

    public function test_clear_cart_removes_all_user_items(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        CartItem::factory()->count(3)->create(['user_id' => $user->id]);
        CartItem::factory()->count(2)->create(['user_id' => $otherUser->id]);

        // Act
        $result = $this->repository->clearCart($user->id);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(0, CartItem::where('user_id', $user->id)->count());
        $this->assertEquals(2, CartItem::where('user_id', $otherUser->id)->count());
    }

    public function test_get_cart_items_count_returns_total_quantity(): void
    {
        // Arrange
        $user = User::factory()->create();

        CartItem::factory()->create(['user_id' => $user->id, 'quantity' => 3]);
        CartItem::factory()->create(['user_id' => $user->id, 'quantity' => 2]);

        // Act
        $result = $this->repository->getCartItemsCount($user->id);

        // Assert
        $this->assertEquals(5, $result);
    }

    public function test_get_cart_item_by_id_returns_item(): void
    {
        // Arrange
        $cartItem = CartItem::factory()->create(['quantity' => 4]);

        // Act
        $result = $this->repository->getCartItemById($cartItem->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($cartItem->id, $result->id);
        $this->assertEquals(4, $result->quantity);
    }

    public function test_get_cart_item_by_user_and_product_returns_item(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Act
        $result = $this->repository->getCartItemByUserAndProduct($user->id, $product->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($cartItem->id, $result->id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
    }
}
