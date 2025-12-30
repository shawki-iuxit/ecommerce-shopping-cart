<?php

namespace App\Domain\Order\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private Order $model
    ) {}

    public function findById(int $id): ?Order
    {
        return $this->model
            ->with(['orderItems.product', 'user'])
            ->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model
            ->with(['orderItems.product', 'user'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function findOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['orderItems.product'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findRecentOrders(int $limit = 10): Collection
    {
        return $this->model
            ->with(['orderItems.product', 'user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }
}
