<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Repositories\FavoriteAdvertisementRepository;

class FavoriteController extends Controller
{
    private FavoriteAdvertisementRepository $favRepo;

    public function __construct()
    {
        parent::__construct();
        $this->favRepo = new FavoriteAdvertisementRepository();
    }

    public function index(): void
    {
        $userId = $this->session->getUserId();
        $favorites = $this->favRepo->findByUserId($userId);

        $this->render('favorites/index', [
            'title' => 'Избранное',
            'favorites' => $favorites,
        ]);
    }

    public function add(int $adId): void
    {
        $userId = $this->session->getUserId();

        if (!$this->favRepo->exists($userId, $adId)) {
            $this->favRepo->add($userId, $adId);
            $this->session->setFlash('success', 'Объявление добавлено в избранное');
        }

        $this->back();
    }

    public function remove(int $adId): void
    {
        $userId = $this->session->getUserId();
        $this->favRepo->remove($userId, $adId);
        $this->session->setFlash('success', 'Объявление удалено из избранного');

        $this->back();
    }
}
