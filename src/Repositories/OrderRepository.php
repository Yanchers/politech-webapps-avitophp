<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;

class OrderRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        $row = $this->db->fetch(
            "SELECT * FROM orders WHERE order_number = ?",
            [$orderNumber]
        );

        if (!$row) {
            return null;
        }

        $order = $this->hydrate($row);
        $order->items = $this->findItemsByOrderId($order->order_id);
        return $order;
    }

    public function findByBuyerId(int $buyerId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC",
            [$buyerId]
        );

        $orders = [];
        foreach ($rows as $row) {
            $order = $this->hydrate($row);
            $order->items = $this->findItemsByOrderId($order->order_id);
            $orders[] = $order;
        }

        return $orders;
    }

    public function findItemsByOrderId(int $orderId): array
    {
        $sql = "SELECT * FROM order_item_view WHERE order_id = ? ORDER BY order_item_id ASC";
        return array_map(fn($row) => $this->hydrateItem($row), $this->db->fetchAll($sql, [$orderId]));
    }

    public function create(int $buyerId, float $totalAmount, array $itemsData): Order
    {
        $itemsJson = json_encode(array_map(fn($item) => [
            'ad_id' => (int)$item['ad_id'],
            'price_paid' => (float)$item['price_paid'],
        ], $itemsData));

        $result = $this->db->fetch("CALL sp_checkout(?, ?, ?)", [
            $buyerId,
            $totalAmount,
            $itemsJson,
        ]);

        $orderId = (int) $result['order_id'];
        $order = $this->hydrate(
            $this->db->fetch("SELECT * FROM orders WHERE order_id = ?", [$orderId])
        );
        $order->items = $this->findItemsByOrderId($orderId);
        return $order;
    }

    public function generateOrderNumber(): string
    {
        $result = $this->db->fetch("SELECT fn_generate_order_number() AS order_number");
        return $result['order_number'];
    }

    private function hydrate(array $row): Order
    {
        $order = new Order();
        foreach ($row as $key => $value) {
            $order->$key = $value;
        }
        return $order;
    }

    private function hydrateItem(array $row): OrderItem
    {
        $item = new OrderItem();
        foreach ($row as $key => $value) {
            $item->$key = $value;
        }
        return $item;
    }
}
