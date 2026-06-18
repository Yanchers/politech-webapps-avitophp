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
        $sql = "SELECT oi.*, a.title AS ad_title,
                       u.first_name AS seller_first_name, u.last_name AS seller_last_name,
                       u.email AS seller_email, u.phone AS seller_phone,
                       ai.image_path AS first_image_path
                FROM order_items oi
                JOIN advertisements a ON oi.ad_id = a.ad_id
                JOIN users u ON a.seller_id = u.user_id
                LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1
                WHERE oi.order_id = ?
                ORDER BY oi.order_item_id ASC";

        return array_map(fn($row) => $this->hydrateItem($row), $this->db->fetchAll($sql, [$orderId]));
    }

    public function create(array $orderData, array $itemsData): Order
    {
        $orderId = $this->db->insert('orders', $orderData);
        $order = $this->hydrate($this->db->fetch("SELECT * FROM orders WHERE order_id = ?", [$orderId]));

        foreach ($itemsData as $item) {
            $item['order_id'] = $orderId;
            $this->db->insert('order_items', $item);
        }

        $order->items = $this->findItemsByOrderId($orderId);
        return $order;
    }

    public function generateOrderNumber(): string
    {
        $number = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
        $existing = $this->db->fetch("SELECT order_id FROM orders WHERE order_number = ?", [$number]);
        if ($existing) {
            return $this->generateOrderNumber();
        }
        return $number;
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
