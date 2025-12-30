<?php

namespace App\Domain\Order\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function findOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findRecentOrders(int $limit = 10): Collection;

    public function create(array $data): Order;
}
