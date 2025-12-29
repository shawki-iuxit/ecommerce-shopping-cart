<?php

namespace App\Domain\Product\Transformers;

use App\Domain\Product\DTOs\CategoryDTO;
use App\Domain\Product\DTOs\ProductListDTO;
use App\Models\Product;

class ProductTransformer
{
    public function transformToListDTO(Product $product): ProductListDTO
    {
        return new ProductListDTO(
            id: $product->id,
            name: $product->name,
            sku: $product->sku,
            description: $product->description ?? '',
            price: (float) $product->price,
            formattedPrice: '$' . number_format($product->price, 2),
            stockQuantity: $product->stock_quantity,
            imageUrl: $product->image_url,
            isActive: $product->is_active,
            isInStock: $product->stock_quantity > 0,
            isAvailable: $product->is_active && $product->stock_quantity > 0,
            category: $product->relationLoaded('category') && $product->category
                ? CategoryDTO::fromModel($product->category)
                : null,
        );
    }

    public function transformCollection(iterable $products): array
    {
        $result = [];
        
        foreach ($products as $product) {
            $result[] = $this->transformToListDTO($product);
        }

        return $result;
    }
}