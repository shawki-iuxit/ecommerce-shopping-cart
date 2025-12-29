<?php

namespace App\Domain\Cart\Repositories;

use App\Models\CartItem;
use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    public function getCartItemsByUserId(int $userId): Collection;

    public function findCartItem(int $userId, int $productId): ?CartItem;

    public function addItemToCart(int $userId, int $productId, int $quantity): CartItem;

    public function updateCartItemQuantity(int $cartItemId, int $quantity): bool;

    public function removeCartItem(int $cartItemId): bool;

    public function clearCart(int $userId): bool;

    public function getCartItemsCount(int $userId): int;
}