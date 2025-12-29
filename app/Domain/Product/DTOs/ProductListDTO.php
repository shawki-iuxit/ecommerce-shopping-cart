<?php

namespace App\Domain\Product\DTOs;

class ProductListDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $sku,
        public readonly string $description,
        public readonly float $price,
        public readonly string $formattedPrice,
        public readonly int $stockQuantity,
        public readonly ?string $imageUrl,
        public readonly bool $isActive,
        public readonly bool $isInStock,
        public readonly bool $isAvailable,
        public readonly ?CategoryDTO $category,
    ) {}
}
