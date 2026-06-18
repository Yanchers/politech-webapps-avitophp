<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CartRepository;

class CartController extends Controller
{
    private CartRepository $basketRepo;
    private AdvertisementRepository $adRepo;

    public function __construct()
    {
        parent::__construct();
        $this->basketRepo = new CartRepository();
        $this->adRepo = new AdvertisementRepository();
    }

    public function index(): void
    {
        $userId = $this->session->getUserId();
        $items = $this->basketRepo->getCartItems($userId);
        $total = array_sum(array_column($items, 'price'));

        $this->render('cart/index', [
            'title' => 'Корзина',
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(int $adId): void
    {
        $userId = $this->session->getUserId();

        $ad = $this->adRepo->findById($adId);

        if (!$ad || $ad->status_id !== 3) {
            $this->session->setFlash('error', 'Объявление не найдено или недоступно для покупки');
            $this->back();
            return;
        }

        if ($ad->seller_id === $userId) {
            $this->session->setFlash('error', 'Нельзя добавить в корзину своё объявление');
            $this->back();
            return;
        }

        if ($this->basketRepo->inBasket($userId, $adId)) {
            $this->session->setFlash('error', 'Этот товар уже в корзине');
            $this->back();
            return;
        }

        $this->basketRepo->add($userId, $adId);
        $this->session->setFlash('success', 'Товар добавлен в корзину');
        $this->back();
    }

    public function remove(int $adId): void
    {
        $userId = $this->session->getUserId();
        $this->basketRepo->remove($userId, $adId);
        $this->session->setFlash('success', 'Товар удалён из корзины');
        $this->back();
    }
}
