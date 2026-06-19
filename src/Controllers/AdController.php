<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Validator;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CityRepository;
use App\Repositories\FavoriteAdvertisementRepository;
use App\Repositories\ItemConditionRepository;
use App\Repositories\UserRepository;

class AdController extends Controller
{
    private AdvertisementRepository $adRepo;
    private CategoryRepository $categoryRepo;
    private CityRepository $cityRepo;
    private FavoriteAdvertisementRepository $favRepo;
    private ItemConditionRepository $conditionRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->adRepo = new AdvertisementRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->cityRepo = new CityRepository();
        $this->favRepo = new FavoriteAdvertisementRepository();
        $this->conditionRepo = new ItemConditionRepository();
        $this->userRepo = new UserRepository();
    }

    public function show(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad || (in_array($ad->status_id, [6]) && (!$this->session->isAuthenticated() || $this->session->getUserId() !== $ad->seller_id))) {
            Response::notFound();
            return;
        }

        $images = $this->adRepo->getImages($id);
        $seller = $this->userRepo->findById($ad->seller_id);

        $isFavorite = false;
        if ($this->session->isAuthenticated()) {
            $isFavorite = $this->favRepo->exists($this->session->getUserId(), $id);
        }

        $this->render('ad/show', [
            'title' => $ad->title,
            'ad' => $ad,
            'images' => $images,
            'seller' => $seller,
            'isFavorite' => $isFavorite,
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryRepo->findParents();
        $categorySubcategories = [];
        foreach ($categories as $cat) {
            $categorySubcategories[$cat->category_id] = $this->categoryRepo->findByParentId($cat->category_id);
        }
        $cities = $this->cityRepo->findAll('name');
        $conditions = $this->conditionRepo->findAll();

        $this->render('ad/create', [
            'title' => 'Создать объявление',
            'categories' => $categories,
            'categorySubcategories' => $categorySubcategories,
            'cities' => $cities,
            'conditions' => $conditions,
        ]);
    }

    public function store(): void
    {
        $data = $this->request->only(['category_id', 'title', 'description', 'price', 'item_condition_id', 'city_id']);

        $validator = new Validator();
        if (!$validator->validate($data, [
            'category_id' => 'required|numeric',
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'item_condition_id' => 'required|numeric',
            'city_id' => 'required|numeric',
        ])) {
            $this->session->setFlash('error', $validator->getFirstError());
            $this->session->set('old_input', $this->request->all());
            $this->back();
            return;
        }

        $ad = $this->adRepo->create([
            'seller_id' => $this->session->getUserId(),
            'category_id' => (int) $data['category_id'],
            'item_condition_id' => (int) $data['item_condition_id'],
            'city_id' => (int) $data['city_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => (float) $data['price'],
            'status_id' => 2,
        ]);

        $this->handleImageUpload($ad->ad_id);

        $this->session->remove('old_input');
        $this->session->setFlash('success', 'Объявление отправлено на модерацию');
        $this->redirect('/ad/' . $ad->ad_id);
    }

    public function edit(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        if ($ad->seller_id !== $this->session->getUserId()) {
            Response::forbidden();
            return;
        }

        $categories = $this->categoryRepo->findParents();
        $categorySubcategories = [];
        foreach ($categories as $cat) {
            $categorySubcategories[$cat->category_id] = $this->categoryRepo->findByParentId($cat->category_id);
        }
        $cities = $this->cityRepo->findAll('name');
        $conditions = $this->conditionRepo->findAll();
        $images = $this->adRepo->getImages($id);

        $this->render('ad/edit', [
            'title' => 'Редактировать объявление',
            'ad' => $ad,
            'categories' => $categories,
            'categorySubcategories' => $categorySubcategories,
            'cities' => $cities,
            'conditions' => $conditions,
            'images' => $images,
        ]);
    }

    public function update(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        if ($ad->seller_id !== $this->session->getUserId()) {
            Response::forbidden();
            return;
        }

        $data = $this->request->only(['category_id', 'title', 'description', 'price', 'item_condition_id', 'city_id']);

        $validator = new Validator();
        if (!$validator->validate($data, [
            'category_id' => 'required|numeric',
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'item_condition_id' => 'required|numeric',
            'city_id' => 'required|numeric',
        ])) {
            $this->session->setFlash('error', $validator->getFirstError());
            $this->back();
            return;
        }

        $this->adRepo->update($id, [
            'category_id' => (int) $data['category_id'],
            'item_condition_id' => (int) $data['item_condition_id'],
            'city_id' => (int) $data['city_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => (float) $data['price'],
            'updated_at' => date('Y-m-d H:i:s'),
            'status_id' => 2,
        ]);

        $deleteImages = $this->request->post('delete_images');
        if (is_array($deleteImages)) {
            foreach ($deleteImages as $imageId) {
                $this->adRepo->deleteImage((int) $imageId);
            }
        }

        $this->handleImageUpload($id);

        $this->session->setFlash('success', 'Объявление обновлено');
        $this->redirect('/ad/' . $id);
    }

    public function destroy(int $id): void
    {
        $ad = $this->adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        if ($ad->seller_id !== $this->session->getUserId()) {
            Response::forbidden();
            return;
        }

        $this->adRepo->update($id, [
            'status_id' => 6,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->setFlash('success', 'Объявление удалено');
        $this->redirect('/');
    }

    public function userAds(): void
    {
        $ads = $this->adRepo->findBySellerId($this->session->getUserId());

        $adImages = [];
        foreach ($ads as $ad) {
            $images = $this->adRepo->getImages($ad->ad_id);
            $adImages[$ad->ad_id] = !empty($images) ? $images[0] : null;
        }

        $this->render('ad/index', [
            'title' => 'Мои объявления',
            'ads' => $ads,
            'adImages' => $adImages,
        ]);
    }

    private function handleImageUpload(int $adId): void
    {
        $files = $this->request->file('images');
        if (!$files) {
            return;
        }

        $appConfig = require __DIR__ . '/../../config/app.php';
        $maxSize = $appConfig['upload_max_size'] ?? 5 * 1024 * 1024;
        $allowed = $appConfig['allowed_extensions'] ?? ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $sortOrder = $this->adRepo->getNextSortOrder($adId);

        $names = $files['name'];
        $tmpNames = $files['tmp_name'];
        $errors = $files['error'];
        $sizes = $files['size'];

        if (!is_array($names)) {
            $names = [$names];
            $tmpNames = [$tmpNames];
            $errors = [$errors];
            $sizes = [$sizes];
        }

        for ($i = 0; $i < count($names); $i++) {
            if ($errors[$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            if ($sizes[$i] > $maxSize) {
                continue;
            }

            $ext = strtolower(pathinfo($names[$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                continue;
            }

            $newName = uniqid('ad_', true) . '.' . $ext;
            $destPath = $uploadDir . $newName;

            if (move_uploaded_file($tmpNames[$i], $destPath)) {
                $this->adRepo->addImage($adId, 'uploads/' . $newName, $sortOrder++);
            }
        }
    }
}
