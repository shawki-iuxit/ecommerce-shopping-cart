<?php

namespace App\Domain\Cart\Repositories;

use App\Models\CartItem;
use Illuminate\Support\Collection;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function __construct(
        private CartItem $model
    ) {}

    public function getCartItemsByUserId(int $userId): Collection
    {
        return $this->model
            ->with(['product.category'])
            ->where('user_id', $userId)
            ->get();
    }

    public function findCartItem(int $userId, int $productId): ?CartItem
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function addItemToCart(int $userId, int $productId, int $quantity): CartItem
    {
        $existingItem = $this->findCartItem($userId, $productId);

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
            ]);

            return $existingItem->fresh();
        }

        return $this->model->create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): bool
    {
        return $this->model
            ->where('id', $cartItemId)
            ->update(['quantity' => $quantity]) > 0;
    }

    public function removeCartItem(int $cartItemId): bool
    {
        return $this->model->destroy($cartItemId) > 0;
    }

    public function clearCart(int $userId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->delete() >= 0;
    }

    public function getCartItemsCount(int $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->sum('quantity');
    }

    public function getCartItemById(int $cartItemId): ?CartItem
    {
        return $this->model->find($cartItemId);
    }

    public function getCartItemByUserAndProduct(int $userId, int $productId): ?CartItem
    {
        return $this->findCartItem($userId, $productId);
    }
}
