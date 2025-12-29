<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\DTOs\CartDTO;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Cart\Transformers\CartTransformer;
use App\Models\Product;

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
            $product = Product::findOrFail($productId);

            // Check if sufficient stock is available
            if ($product->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock. Only {$product->stock_quantity} items available.");
            }

            // Check if item already exists in cart
            $existingCartItem = $this->cartRepository->getCartItemByUserAndProduct($userId, $productId);
            if ($existingCartItem) {
                $newQuantity = $existingCartItem->quantity + $quantity;
                if ($product->stock_quantity < $newQuantity) {
                    throw new \Exception("Insufficient stock. Only {$product->stock_quantity} items available.");
                }
            }

            $this->cartRepository->addItemToCart($userId, $productId, $quantity);

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $cartItemId);
        }

        try {
            $cartItem = $this->cartRepository->getCartItemById($cartItemId);
            if (! $cartItem || $cartItem->user_id !== $userId) {
                throw new \Exception('Cart item not found.');
            }

            $product = Product::findOrFail($cartItem->product_id);

            // Check if sufficient stock is available
            if ($product->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock. Only {$product->stock_quantity} items available.");
            }

            return $this->cartRepository->updateCartItemQuantity($cartItemId, $quantity);
        } catch (\Exception $e) {
            throw $e;
        }
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
