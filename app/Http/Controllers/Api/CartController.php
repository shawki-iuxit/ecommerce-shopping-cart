<?php

namespace App\Http\Controllers\Api;

use App\Domain\Cart\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->getCart($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        try {
            $success = $this->cartService->addToCart(
                $request->user()->id,
                $request->product_id,
                $request->get('quantity', 1)
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add item to cart'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'cart_count' => $this->cartService->getCartItemsCount($request->user()->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        try {
            $success = $this->cartService->updateQuantity(
                $request->user()->id,
                $id,
                $request->quantity
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update cart item'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => $this->cartService->getCartItemsCount($request->user()->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->cartService->removeItem($request->user()->id, $id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove cart item'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->cartService->getCartItemsCount($request->user()->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove cart item',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function clear(Request $request): JsonResponse
    {
        try {
            $this->cartService->clearCart($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function count(Request $request): JsonResponse
    {
        try {
            $count = $this->cartService->getCartItemsCount($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart count',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
