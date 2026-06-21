<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Services\MailService;

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

        $totalAmount = array_sum(array_column($items, 'price'));

        $itemsData = array_map(fn($item) => [
            'ad_id' => $item['ad_id'],
            'price_paid' => $item['price'],
        ], $items);

        try {
            $order = $this->orderRepo->create($userId, $totalAmount, $itemsData);
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Ошибка при оформлении заказа. Попробуйте снова.');
            $this->redirect('/cart');
            return;
        }

        $this->sendOrderEmail($user, $order->order_number, $items);

        $this->session->set('last_order_number', $order->order_number);
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
        $to = $user['email'] ?? '';
        if (empty($to)) return;

        $config = require __DIR__ . '/../../config/app.php';
        $apiKey = $config['brevo_api_key'] ?? '';

        if (empty($apiKey)) return;

        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $senderEmail = 'oreshko2001@gmail.com';

        $mailer = new MailService($apiKey, $senderEmail, $config['name'] ?? 'Jan');
        $mailer->sendOrderConfirmation($to, $name, $orderNumber, $items);
    }
}
