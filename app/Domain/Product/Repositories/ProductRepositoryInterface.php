<?php

namespace App\Domain\Product\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findAll(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Product;

    public function findByCategoryId(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    public function findActiveProducts(int $perPage = 15): LengthAwarePaginator;

    public function searchProducts(string $query, int $perPage = 15): LengthAwarePaginator;

    public function getFeaturedProducts(int $limit = 8): Collection;
}