<?php

namespace App\Http\Controllers\Api;

use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'shipping_address.first_name' => 'required|string|max:255',
            'shipping_address.last_name' => 'required|string|max:255',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.zip_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:255',
            'billing_address.first_name' => 'required|string|max:255',
            'billing_address.last_name' => 'required|string|max:255',
            'billing_address.address_line_1' => 'required|string|max:255',
            'billing_address.address_line_2' => 'nullable|string|max:255',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.state' => 'required|string|max:255',
            'billing_address.zip_code' => 'required|string|max:20',
            'billing_address.country' => 'required|string|max:255',
        ]);

        try {
            $order = $this->orderService->placeOrder(
                $request->user(),
                $validatedData['shipping_address'],
                $validatedData['billing_address']
            );

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'tax_amount' => $order->tax_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'placed_at' => $order->placed_at,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product' => [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'image_url' => $item->product->image_url,
                            ],
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ];
                    }),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'placed_at' => $order->placed_at,
                    'items_count' => $order->orderItems->count(),
                ];
            }),
        ]);
    }

    public function show(Request $request, int $orderId): JsonResponse
    {
        $order = $request->user()
            ->orders()
            ->with('orderItems.product')
            ->findOrFail($orderId);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'tax_amount' => $order->tax_amount,
                'shipping_amount' => $order->shipping_amount,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'placed_at' => $order->placed_at,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'image_url' => $item->product->image_url,
                        ],
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                }),
            ],
        ]);
    }
}
