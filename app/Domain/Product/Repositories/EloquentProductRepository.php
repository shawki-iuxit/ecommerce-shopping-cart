<?php

namespace App\Domain\Product\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Product $model
    ) {}

    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['category'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return $this->model
            ->with(['category'])
            ->find($id);
    }

    public function findByCategoryId(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['category'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }

    public function findActiveProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['category'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->paginate($perPage);
    }

    public function searchProducts(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['category'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return $this->model
            ->with(['category'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
