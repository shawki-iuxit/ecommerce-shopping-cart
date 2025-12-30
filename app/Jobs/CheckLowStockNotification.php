<?php

namespace App\Jobs;

use App\Mail\LowStockNotification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class CheckLowStockNotification implements ShouldQueue
{
    use Queueable;

    private const LOW_STOCK_THRESHOLD = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Only send notification if stock is at or below threshold
        if ($this->product->stock_quantity <= self::LOW_STOCK_THRESHOLD) {
            // Find admin user
            $admin = User::where('email', 'shawki.course@gmail.com')->first();
            
            if ($admin) {
                Mail::to($admin)->send(new LowStockNotification($this->product));
            }
        }
    }
}
