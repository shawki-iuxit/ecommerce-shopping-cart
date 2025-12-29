<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ProductListDTO;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Product\Transformers\ProductTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $transformer
    ) {}

    public function getAllProducts(int $perPage = 15): LengthAwarePaginator
    {
        $products = $this->productRepository->findActiveProducts($perPage);

        return $products->through(function ($product) {
            return $this->transformer->transformToListDTO($product);
        });
    }

    public function getProductById(int $id): ?ProductListDTO
    {
        $product = $this->productRepository->findById($id);

        return $product ? $this->transformer->transformToListDTO($product) : null;
    }

    public function getProductsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        $products = $this->productRepository->findByCategoryId($categoryId, $perPage);

        return $products->through(function ($product) {
            return $this->transformer->transformToListDTO($product);
        });
    }

    public function searchProducts(string $query, int $perPage = 15): LengthAwarePaginator
    {
        $products = $this->productRepository->searchProducts($query, $perPage);

        return $products->through(function ($product) {
            return $this->transformer->transformToListDTO($product);
        });
    }

    public function getFeaturedProducts(int $limit = 8): Collection
    {
        $products = $this->productRepository->getFeaturedProducts($limit);

        return $products->map(function ($product) {
            return $this->transformer->transformToListDTO($product);
        });
    }
}