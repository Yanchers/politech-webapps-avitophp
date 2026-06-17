<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CategoryRepository;

class HomeController extends Controller
{
    private AdvertisementRepository $adRepo;
    private CategoryRepository $categoryRepo;
    
    public function __construct()
    {
        parent::__construct();
        $this->adRepo = new AdvertisementRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    public function index(): void
    {
        $activeAds = $this->adRepo->findAllActive(limit: 8, offset: 0);
        $categories = $this->categoryRepo->findParents();

        $this->render('home', [
            'title' => 'Главная — Avito PHP',
            'ads' => $activeAds,
            'categories' => $categories,
        ]);
    }
}
