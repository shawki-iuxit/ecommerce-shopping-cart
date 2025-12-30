<?php

namespace Tests\Unit\Domain\Product\Repositories;

use App\Domain\Product\Repositories\EloquentProductRepository;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProductRepository(new Product);
    }

    public function test_find_all_returns_paginated_products(): void
    {
        // Arrange
        Product::factory()->count(25)->create(['is_active' => true]);
        Product::factory()->count(5)->create(['is_active' => false]);

        // Act
        $result = $this->repository->findAll(10);

        // Assert
        $this->assertEquals(10, $result->count());
        $this->assertEquals(30, $result->total());
        $this->assertEquals(3, $result->lastPage());

        // Verify relationships are loaded
        $firstProduct = $result->first();
        $this->assertTrue($firstProduct->relationLoaded('category'));
    }

    public function test_find_by_id_returns_product_with_category(): void
    {
        // Arrange
        $category = Category::factory()->create(['name' => 'Test Category']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
        ]);

        // Act
        $result = $this->repository->findById($product->id);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals('Test Product', $result->name);
        $this->assertTrue($result->relationLoaded('category'));
        $this->assertEquals('Test Category', $result->category->name);
    }

    public function test_find_by_id_returns_null_for_non_existent_product(): void
    {
        // Act
        $result = $this->repository->findById(999);

        // Assert
        $this->assertNull($result);
    }

    public function test_find_active_products_excludes_inactive_products(): void
    {
        // Arrange
        Product::factory()->count(10)->create(['is_active' => true]);
        Product::factory()->count(5)->create(['is_active' => false]);

        // Act
        $result = $this->repository->findActiveProducts();

        // Assert
        $this->assertEquals(10, $result->total());

        foreach ($result as $product) {
            $this->assertTrue($product->is_active);
            $this->assertTrue($product->relationLoaded('category'));
        }
    }

    public function test_search_products_finds_by_name(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'name' => 'iPad Air',
            'is_active' => true,
        ]);

        // Act
        $result = $this->repository->searchProducts('iPhone');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertStringContainsString('iPhone', $result->first()->name);
    }

    public function test_search_products_finds_by_description(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Smartphone',
            'description' => 'Latest iPhone technology with advanced features',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'name' => 'Tablet',
            'description' => 'Android powered device',
            'is_active' => true,
        ]);

        // Act
        $result = $this->repository->searchProducts('iPhone');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertStringContainsString('iPhone', $result->first()->description);
    }

    public function test_search_products_finds_by_sku(): void
    {
        // Arrange
        Product::factory()->create([
            'sku' => 'APL123456',
            'name' => 'Test Product',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'sku' => 'SAM789012',
            'name' => 'Other Product',
            'is_active' => true,
        ]);

        // Act
        $result = $this->repository->searchProducts('APL123');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('APL123456', $result->first()->sku);
    }

    public function test_search_products_excludes_inactive_products(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'iPhone Active',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'name' => 'iPhone Inactive',
            'is_active' => false,
        ]);

        // Act
        $result = $this->repository->searchProducts('iPhone');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('iPhone Active', $result->first()->name);
        $this->assertTrue($result->first()->is_active);
    }

    public function test_search_products_case_insensitive(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'iPhone Pro Max',
            'is_active' => true,
        ]);

        // Act
        $result = $this->repository->searchProducts('iphone');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertStringContainsStringIgnoringCase('iphone', $result->first()->name);
    }

    public function test_get_featured_products_returns_random_in_stock_products(): void
    {
        // Arrange
        Product::factory()->count(10)->create([
            'is_active' => true,
            'stock_quantity' => 5,
        ]);

        Product::factory()->count(5)->create([
            'is_active' => true,
            'stock_quantity' => 0,
        ]);

        Product::factory()->count(3)->create([
            'is_active' => false,
            'stock_quantity' => 5,
        ]);

        // Act
        $result = $this->repository->getFeaturedProducts(8);

        // Assert
        $this->assertEquals(8, $result->count());

        foreach ($result as $product) {
            $this->assertTrue($product->is_active);
            $this->assertGreaterThan(0, $product->stock_quantity);
            $this->assertTrue($product->relationLoaded('category'));
        }
    }

    public function test_products_ordered_by_latest(): void
    {
        // Arrange
        $oldProduct = Product::factory()->create([
            'name' => 'Old Product',
            'is_active' => true,
            'created_at' => now()->subDays(2),
        ]);

        $newProduct = Product::factory()->create([
            'name' => 'New Product',
            'is_active' => true,
            'created_at' => now(),
        ]);

        // Act
        $result = $this->repository->findActiveProducts();

        // Assert
        $this->assertEquals('New Product', $result->first()->name);
        $this->assertEquals('Old Product', $result->last()->name);
    }
}
