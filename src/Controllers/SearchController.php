<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CityRepository;
use App\Repositories\ItemConditionRepository;

class SearchController extends Controller
{
    private AdvertisementRepository $adRepo;
    private CategoryRepository $categoryRepo;
    private CityRepository $cityRepo;
    private ItemConditionRepository $conditionRepo;

    public function __construct()
    {
        parent::__construct();
        $this->adRepo = new AdvertisementRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->cityRepo = new CityRepository();
        $this->conditionRepo = new ItemConditionRepository();
    }

    public function index(): void
    {
        $search = $this->request->get('q', '');
        $categoryId = $this->request->get('category_id') ? (int) $this->request->get('category_id') : null;
        $cityId = $this->request->get('city_id') ? (int) $this->request->get('city_id') : null;
        $itemConditionId = $this->request->get('item_condition_id') ? (int) $this->request->get('item_condition_id') : null;
        $priceMin = $this->request->get('price_min') !== null ? (float) $this->request->get('price_min') : null;
        $priceMax = $this->request->get('price_max') !== null ? (float) $this->request->get('price_max') : null;
        $sort = $this->request->get('sort', 'date_desc');
        $page = $this->request->get('page') ? max(1, (int) $this->request->get('page')) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $this->adRepo->countActive(
            $categoryId, $cityId, $search ?: null,
            $itemConditionId, $priceMin, $priceMax
        );

        $ads = $this->adRepo->findAllActive(
            $categoryId, $cityId, $search ?: null,
            $itemConditionId, $priceMin, $priceMax,
            $sort, $perPage, $offset
        );

        $categories = $this->categoryRepo->findParents();
        $categorySubcategories = [];
        foreach ($categories as $cat) {
            $categorySubcategories[$cat->category_id] = $this->categoryRepo->findByParentId($cat->category_id);
        }
        $cities = $this->cityRepo->findAll('name');
        $conditions = $this->conditionRepo->findAll();

        $totalPages = (int) ceil($total / $perPage);

        $this->render('search/index', [
            'title' => $search ? "Поиск: {$search}" : 'Поиск объявлений',
            'ads' => $ads,
            'categories' => $categories,
            'categorySubcategories' => $categorySubcategories,
            'cities' => $cities,
            'conditions' => $conditions,
            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedCity' => $cityId,
            'selectedCondition' => $itemConditionId,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }
}
