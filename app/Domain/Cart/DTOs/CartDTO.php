<?php

namespace App\Domain\Cart\DTOs;

class CartDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly array $items,
        public readonly int $totalItems,
        public readonly float $totalAmount,
        public readonly string $formattedTotalAmount,
    ) {}
}
