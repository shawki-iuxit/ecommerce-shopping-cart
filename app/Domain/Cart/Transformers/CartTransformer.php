<?php

namespace App\Domain\Cart\Transformers;

use App\Domain\Cart\DTOs\CartDTO;
use App\Domain\Cart\DTOs\CartItemDTO;
use App\Domain\Product\Transformers\ProductTransformer;
use App\Models\CartItem;
use Illuminate\Support\Collection;

class CartTransformer
{
    public function __construct(
        private ProductTransformer $productTransformer
    ) {}

    public function transformCartItem(CartItem $cartItem): CartItemDTO
    {
        $product = $this->productTransformer->transformToListDTO($cartItem->product);

        return new CartItemDTO(
            id: $cartItem->id,
            userId: $cartItem->user_id,
            productId: $cartItem->product_id,
            quantity: $cartItem->quantity,
            product: $product,
            subtotal: $product->price * $cartItem->quantity,
        );
    }

    public function transformCart(int $userId, Collection $cartItems): CartDTO
    {
        $items = $cartItems->map(fn(CartItem $item) => $this->transformCartItem($item))->toArray();
        $totalItems = $cartItems->sum('quantity');
        $totalAmount = array_sum(array_map(fn(CartItemDTO $item) => $item->subtotal, $items));

        return new CartDTO(
            userId: $userId,
            items: $items,
            totalItems: $totalItems,
            totalAmount: $totalAmount,
            formattedTotalAmount: '$' . number_format($totalAmount, 2),
        );
    }
}