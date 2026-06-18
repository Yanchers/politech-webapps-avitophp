<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    private Database $db;
    private OrderRepository $orderRepo;
    private CartRepository $basketRepo;
    private AdvertisementRepository $adRepo;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->orderRepo = new OrderRepository();
        $this->basketRepo = new CartRepository();
        $this->adRepo = new AdvertisementRepository();
    }

    public function create(): void
    {
        $userId = $this->session->getUserId();
        $items = $this->basketRepo->getCartItemsWithSeller($userId);

        if (empty($items)) {
            $this->session->setFlash('error', 'Корзина пуста');
            $this->redirect('/cart');
            return;
        }

        $total = array_sum(array_column($items, 'price'));

        $this->render('order/create', [
            'title' => 'Оформление заказа',
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function store(): void
    {
        $userId = $this->session->getUserId();
        $user = $this->session->getUser();

        $items = $this->basketRepo->getActiveCartItems($userId);

        if (empty($items)) {
            $this->session->setFlash('error', 'Корзина пуста');
            $this->redirect('/cart');
            return;
        }

        $orderNumber = $this->orderRepo->generateOrderNumber();
        $totalAmount = array_sum(array_column($items, 'price'));

        $this->db->beginTransaction();

        try {
            $orderData = [
                'order_number' => $orderNumber,
                'buyer_id' => $userId,
                'total_amount' => $totalAmount,
            ];

            $itemsData = [];
            foreach ($items as $item) {
                $itemsData[] = [
                    'ad_id' => $item['ad_id'],
                    'price_paid' => $item['price'],
                ];

                $this->adRepo->update($item['ad_id'], ['status_id' => 5]);
            }

            $this->orderRepo->create($orderData, $itemsData);
            $this->basketRepo->clear($userId);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->session->setFlash('error', 'Ошибка при оформлении заказа. Попробуйте снова.');
            $this->redirect('/cart');
            return;
        }

        $this->sendOrderEmail($user, $orderNumber, $items);

        $this->session->set('last_order_number', $orderNumber);
        $this->session->setFlash('success', 'Заказ успешно оформлен!');
        $this->redirect('/order/success');
    }

    public function success(): void
    {
        $orderNumber = $this->session->get('last_order_number');

        if (!$orderNumber) {
            $this->redirect('/orders');
            return;
        }

        $order = $this->orderRepo->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('/orders');
            return;
        }

        $this->render('order/success', [
            'title' => 'Заказ оформлен',
            'order' => $order,
        ]);
        $this->session->remove('last_order_number');
    }

    public function index(): void
    {
        $userId = $this->session->getUserId();
        $orders = $this->orderRepo->findByBuyerId($userId);

        $this->render('order/index', [
            'title' => 'Мои заказы',
            'orders' => $orders,
        ]);
    }

    public function show(string $orderNumber): void
    {
        $userId = $this->session->getUserId();
        $order = $this->orderRepo->findByOrderNumber($orderNumber);

        if (!$order || $order->buyer_id !== $userId) {
            $this->session->setFlash('error', 'Заказ не найден');
            $this->redirect('/orders');
            return;
        }

        $this->render('order/show', [
            'title' => 'Заказ ' . $orderNumber,
            'order' => $order,
        ]);
    }

    private function sendOrderEmail(array $user, string $orderNumber, array $items): void
    {
        $appName = $this->getLayoutData()['app']['name'] ?? 'Avito PHP';
        $to = $user['email'] ?? '';
        if (empty($to)) return;

        $subject = "Заказ №{$orderNumber} оформлен — {$appName}";

        $body = "<h1>Спасибо за покупку!</h1>";
        $body .= "<p>Ваш заказ №<strong>{$orderNumber}</strong> успешно оформлен.</p>";
        $body .= "<h2>Состав заказа:</h2>";
        $body .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;width:100%;'>";
        $body .= "<tr><th>Товар</th><th>Цена</th></tr>";

        $total = 0;
        foreach ($items as $item) {
            $body .= "<tr><td>" . htmlspecialchars($item['title']) . "</td><td>" . number_format($item['price'], 0, ',', ' ') . " ₽</td></tr>";
            $total += $item['price'];
        }
        $body .= "<tr><td><strong>Итого</strong></td><td><strong>" . number_format($total, 0, ',', ' ') . " ₽</strong></td></tr>";
        $body .= "</table>";
        $body .= "<p>Связаться с продавцами можно через чат на сайте.</p>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        $headers .= "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'avito-php.local') . "\r\n";

        mail($to, $subject, $body, $headers);
    }
}
