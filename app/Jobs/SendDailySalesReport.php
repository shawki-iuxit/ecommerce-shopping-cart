<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?string $date = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $reportDate = $this->date ? Carbon::parse($this->date) : Carbon::today();
        $reportDateString = $reportDate->format('F j, Y');

        // Get all orders from the specified date
        $orders = Order::with(['orderItems.product', 'user'])
            ->whereDate('created_at', $reportDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare sales data
        $salesData = $this->prepareSalesData($orders);

        // Find admin user
        $admin = User::where('email', 'shawki.course@gmail.com')->first();

        if ($admin) {
            Mail::to($admin)->send(new DailySalesReport($salesData, $reportDateString));
        } else {
            Log::warning('Admin user not found');
        }
        
        Log::info('SendDailySalesReport job completed');
    }

    private function prepareSalesData($orders): array
    {
        if ($orders->isEmpty()) {
            return [
                'orders' => [],
                'products' => [],
                'total_revenue' => 0,
                'total_items_sold' => 0,
                'average_order_value' => 0,
            ];
        }

        // Calculate totals
        $totalRevenue = $orders->sum('total_amount');
        $totalItemsSold = $orders->sum(function ($order) {
            return $order->orderItems->sum('quantity');
        });

        // Aggregate products sold
        $productsSold = [];
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $productKey = $item->product_id;

                if (! isset($productsSold[$productKey])) {
                    $productsSold[$productKey] = [
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'unit_price' => $item->unit_price,
                        'quantity_sold' => 0,
                        'total_revenue' => 0,
                    ];
                }

                $productsSold[$productKey]['quantity_sold'] += $item->quantity;
                $productsSold[$productKey]['total_revenue'] += $item->total_price;
            }
        }

        // Format orders for display
        $ordersData = $orders->map(function ($order) {
            return [
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'items_count' => $order->orderItems->count(),
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->format('g:i A'),
            ];
        })->toArray();

        return [
            'orders' => $ordersData,
            'products' => array_values($productsSold),
            'total_revenue' => $totalRevenue,
            'total_items_sold' => $totalItemsSold
        ];
    }
}
