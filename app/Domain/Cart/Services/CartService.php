<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\DTOs\CartDTO;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Cart\Transformers\CartTransformer;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private CartTransformer $transformer
    ) {}

    public function getCart(int $userId): CartDTO
    {
        $cartItems = $this->cartRepository->getCartItemsByUserId($userId);
        return $this->transformer->transformCart($userId, $cartItems);
    }

    public function addToCart(int $userId, int $productId, int $quantity = 1): bool
    {
        try {
            $this->cartRepository->addItemToCart($userId, $productId, $quantity);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $cartItemId);
        }

        return $this->cartRepository->updateCartItemQuantity($cartItemId, $quantity);
    }

    public function removeItem(int $userId, int $cartItemId): bool
    {
        return $this->cartRepository->removeCartItem($cartItemId);
    }

    public function clearCart(int $userId): bool
    {
        return $this->cartRepository->clearCart($userId);
    }

    public function getCartItemsCount(int $userId): int
    {
        return $this->cartRepository->getCartItemsCount($userId);
    }
}