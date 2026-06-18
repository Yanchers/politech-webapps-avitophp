<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Repositories\AdvertisementRepository;

class ModerationController extends Controller
{
    private AdvertisementRepository $adRepo;

    public function __construct()
    {
        parent::__construct();
        $this->adRepo = new AdvertisementRepository();
    }

    public function index(): void
    {
        $ads = $this->adRepo->findByStatus(2);

        $this->render('moderation/index', [
            'title' => 'Модерация объявлений',
            'ads' => $ads,
        ]);
    }

    public function approve(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $this->adRepo->update($id, [
            'status_id' => 3,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->setFlash('success', 'Объявление одобрено');
        $this->redirect('/moderation');
    }

    public function reject(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $this->adRepo->update($id, [
            'status_id' => 4,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->setFlash('success', 'Объявление отклонено');
        $this->redirect('/moderation');
    }
}
