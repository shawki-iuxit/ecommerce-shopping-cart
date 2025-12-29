<?php

namespace App\Domain\Cart\DTOs;

use App\Domain\Product\DTOs\ProductListDTO;

class CartItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly int $productId,
        public readonly int $quantity,
        public readonly ProductListDTO $product,
        public readonly float $subtotal,
    ) {}
}
