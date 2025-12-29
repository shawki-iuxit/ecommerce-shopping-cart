<?php

namespace App\Domain\Order\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function placeOrder(User $user, array $shippingAddress, array $billingAddress): Order
    {
        return DB::transaction(function () use ($user, $shippingAddress, $billingAddress) {
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Validate stock availability
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                    throw new \Exception("Insufficient stock for {$cartItem->product->name}");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            $taxAmount = $subtotal * 0.1; // 10% tax
            $shippingAmount = 10.00; // Flat shipping rate
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
            ]);

            // Create order items and update inventory
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                    'total_price' => $cartItem->quantity * $cartItem->product->price,
                ]);

                // Update product inventory
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            $user->cartItems()->delete();

            return $order->load('orderItems.product');
        });
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-'.date('Y').'-'.strtoupper(Str::random(8));
    }
}
