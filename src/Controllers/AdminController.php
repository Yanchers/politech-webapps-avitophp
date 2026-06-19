<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Repositories\AdvertisementRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CityRepository;
use App\Repositories\ItemConditionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\ChatMessageRepository;

class AdminController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────

    public function dashboard(): void
    {
        $stats = [
            'users' => (new UserRepository())->count(),
            'ads' => (new AdvertisementRepository())->count(),
            'cities' => (new CityRepository())->count(),
            'categories' => (new CategoryRepository())->count(),
            'conditions' => (new ItemConditionRepository())->count(),
            'messages' => (new ChatMessageRepository())->count(),
        ];

        $this->renderAdmin('admin/dashboard', [
            'title' => 'Админ-панель',
            'stats' => $stats,
        ]);
    }

    // ─── Cities ───────────────────────────────────────────

    public function citiesIndex(): void
    {
        $items = (new CityRepository())->findAll('name');
        $this->renderAdmin('admin/cities/index', [
            'title' => 'Города',
            'items' => $items,
        ]);
    }

    public function citiesCreate(): void
    {
        $this->renderAdmin('admin/cities/create', [
            'title' => 'Создать город',
        ]);
    }

    public function citiesStore(): void
    {
        $name = trim($this->request->post('name'));
        if (!$name) {
            $this->session->setFlash('error', 'Название города не может быть пустым');
            $this->redirect('/admin/cities/create');
            return;
        }
        (new CityRepository())->create($name);
        $this->session->setFlash('success', 'Город создан');
        $this->redirect('/admin/cities');
    }

    public function citiesEdit(int $id): void
    {
        $item = (new CityRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }
        $this->renderAdmin('admin/cities/edit', [
            'title' => 'Редактировать город',
            'item' => $item,
        ]);
    }

    public function citiesUpdate(int $id): void
    {
        $item = (new CityRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }

        $name = trim($this->request->post('name'));
        if (!$name) {
            $this->session->setFlash('error', 'Название города не может быть пустым');
            $this->redirect("/admin/cities/{$id}/edit");
            return;
        }
        (new CityRepository())->update($id, $name);
        $this->session->setFlash('success', 'Город обновлён');
        $this->redirect('/admin/cities');
    }

    public function citiesDelete(int $id): void
    {
        (new CityRepository())->delete($id);
        $this->session->setFlash('success', 'Город удалён');
        $this->redirect('/admin/cities');
    }

    public function citiesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            (new CityRepository())->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Города удалены');
        $this->redirect('/admin/cities');
    }

    // ─── Categories ──────────────────────────────────────

    public function categoriesIndex(): void
    {
        $items = (new CategoryRepository())->findAll();
        $this->renderAdmin('admin/categories/index', [
            'title' => 'Категории',
            'items' => $items,
        ]);
    }

    public function categoriesCreate(): void
    {
        $parents = (new CategoryRepository())->findParents();
        $this->renderAdmin('admin/categories/create', [
            'title' => 'Создать категорию',
            'parents' => $parents,
        ]);
    }

    public function categoriesStore(): void
    {
        $name = trim($this->request->post('name'));
        $parentId = $this->request->post('parent_id');

        if (!$name) {
            $this->session->setFlash('error', 'Название категории не может быть пустым');
            $this->redirect('/admin/categories/create');
            return;
        }

        $data = ['name' => $name];
        if ($parentId !== '' && $parentId !== null) {
            $data['parent_id'] = (int) $parentId;
        }

        (new CategoryRepository())->create($data);
        $this->session->setFlash('success', 'Категория создана');
        $this->redirect('/admin/categories');
    }

    public function categoriesEdit(int $id): void
    {
        $item = (new CategoryRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }
        $parents = (new CategoryRepository())->findParents();
        $this->renderAdmin('admin/categories/edit', [
            'title' => 'Редактировать категорию',
            'item' => $item,
            'parents' => $parents,
        ]);
    }

    public function categoriesUpdate(int $id): void
    {
        $item = (new CategoryRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }

        $name = trim($this->request->post('name'));
        $parentId = $this->request->post('parent_id');

        if (!$name) {
            $this->session->setFlash('error', 'Название категории не может быть пустым');
            $this->redirect("/admin/categories/{$id}/edit");
            return;
        }

        $data = ['name' => $name];
        if ($parentId !== '' && $parentId !== null) {
            $data['parent_id'] = (int) $parentId;
        } else {
            $data['parent_id'] = null;
        }

        (new CategoryRepository())->update($id, $data);
        $this->session->setFlash('success', 'Категория обновлена');
        $this->redirect('/admin/categories');
    }

    public function categoriesDelete(int $id): void
    {
        (new CategoryRepository())->delete($id);
        $this->session->setFlash('success', 'Категория удалена');
        $this->redirect('/admin/categories');
    }

    public function categoriesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            (new CategoryRepository())->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Категории удалены');
        $this->redirect('/admin/categories');
    }

    // ─── Item Conditions ─────────────────────────────────

    public function itemConditionsIndex(): void
    {
        $items = (new ItemConditionRepository())->findAll();
        $this->renderAdmin('admin/item_conditions/index', [
            'title' => 'Состояния товара',
            'items' => $items,
        ]);
    }

    public function itemConditionsCreate(): void
    {
        $this->renderAdmin('admin/item_conditions/create', [
            'title' => 'Создать состояние',
        ]);
    }

    public function itemConditionsStore(): void
    {
        $name = trim($this->request->post('name'));
        if (!$name) {
            $this->session->setFlash('error', 'Название не может быть пустым');
            $this->redirect('/admin/item_conditions/create');
            return;
        }
        (new ItemConditionRepository())->create($name);
        $this->session->setFlash('success', 'Состояние создано');
        $this->redirect('/admin/item_conditions');
    }

    public function itemConditionsEdit(int $id): void
    {
        $item = (new ItemConditionRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }
        $this->renderAdmin('admin/item_conditions/edit', [
            'title' => 'Редактировать состояние',
            'item' => $item,
        ]);
    }

    public function itemConditionsUpdate(int $id): void
    {
        $item = (new ItemConditionRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }

        $name = trim($this->request->post('name'));
        if (!$name) {
            $this->session->setFlash('error', 'Название не может быть пустым');
            $this->redirect("/admin/item_conditions/{$id}/edit");
            return;
        }
        (new ItemConditionRepository())->update($id, $name);
        $this->session->setFlash('success', 'Состояние обновлено');
        $this->redirect('/admin/item_conditions');
    }

    public function itemConditionsDelete(int $id): void
    {
        (new ItemConditionRepository())->delete($id);
        $this->session->setFlash('success', 'Состояние удалено');
        $this->redirect('/admin/item_conditions');
    }

    public function itemConditionsBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            (new ItemConditionRepository())->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Состояния удалены');
        $this->redirect('/admin/item_conditions');
    }

    // ─── Users ───────────────────────────────────────────

    public function usersIndex(): void
    {
        $rows = (new UserRepository())->findAllWithRolesAndCities();
        $this->renderAdmin('admin/users/index', [
            'title' => 'Пользователи',
            'items' => $rows,
        ]);
    }

    public function usersCreate(): void
    {
        $roles = (new RoleRepository())->findAll();
        $cities = (new CityRepository())->findAll('name');
        $this->renderAdmin('admin/users/create', [
            'title' => 'Создать пользователя',
            'roles' => $roles,
            'cities' => $cities,
        ]);
    }

    public function usersStore(): void
    {
        $data = $this->request->only(['email', 'phone', 'password', 'first_name', 'last_name', 'role_id', 'city_id']);

        if (!trim($data['email']) || !trim($data['password']) || !trim($data['first_name']) || !trim($data['last_name'])) {
            $this->session->setFlash('error', 'Заполните все обязательные поля');
            $this->redirect('/admin/users/create');
            return;
        }

        $userRepo = new UserRepository();
        $existing = $userRepo->findByEmail(trim($data['email']));
        if ($existing) {
            $this->session->setFlash('error', 'Пользователь с таким email уже существует');
            $this->redirect('/admin/users/create');
            return;
        }

        $userRepo->create([
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'role_id' => (int) ($data['role_id'] ?? 1),
            'city_id' => (int) ($data['city_id'] ?? 1),
        ]);

        $this->session->setFlash('success', 'Пользователь создан');
        $this->redirect('/admin/users');
    }

    public function usersEdit(int $id): void
    {
        $user = (new UserRepository())->findById($id);
        if (!$user) {
            Response::notFound();
            return;
        }
        $roles = (new RoleRepository())->findAll();
        $cities = (new CityRepository())->findAll('name');
        $this->renderAdmin('admin/users/edit', [
            'title' => 'Редактировать пользователя',
            'item' => $user,
            'roles' => $roles,
            'cities' => $cities,
        ]);
    }

    public function usersUpdate(int $id): void
    {
        $user = (new UserRepository())->findById($id);
        if (!$user) {
            Response::notFound();
            return;
        }

        $data = $this->request->only(['email', 'phone', 'first_name', 'last_name', 'role_id', 'city_id']);
        $password = $this->request->post('password');

        if (!trim($data['email']) || !trim($data['first_name']) || !trim($data['last_name'])) {
            $this->session->setFlash('error', 'Заполните все обязательные поля');
            $this->redirect("/admin/users/{$id}/edit");
            return;
        }

        $updateData = [
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'role_id' => (int) ($data['role_id'] ?? 1),
            'city_id' => (int) ($data['city_id'] ?? 1),
        ];

        if (!empty($password)) {
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        (new UserRepository())->update($id, $updateData);
        $this->session->setFlash('success', 'Пользователь обновлён');
        $this->redirect('/admin/users');
    }

    public function usersDelete(int $id): void
    {
        (new UserRepository())->delete($id);
        $this->session->setFlash('success', 'Пользователь удалён');
        $this->redirect('/admin/users');
    }

    public function usersBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            (new UserRepository())->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Пользователи удалены');
        $this->redirect('/admin/users');
    }

    // ─── Advertisements ──────────────────────────────────

    public function advertisementsIndex(): void
    {
        $rows = (new AdvertisementRepository())->findAllWithSellers();
        $this->renderAdmin('admin/advertisements/index', [
            'title' => 'Объявления',
            'items' => $rows,
        ]);
    }

    public function advertisementsCreate(): void
    {
        $categories = $this->getCategoryTree();
        $cities = (new CityRepository())->findAll('name');
        $conditions = (new ItemConditionRepository())->findAll();
        $users = (new UserRepository())->findAllSimple();
        $statuses = (new AdvertisementRepository())->getAllStatuses();

        $this->renderAdmin('admin/advertisements/create', [
            'title' => 'Создать объявление',
            'categories' => $categories,
            'cities' => $cities,
            'conditions' => $conditions,
            'users' => $users,
            'statuses' => $statuses,
        ]);
    }

    public function advertisementsStore(): void
    {
        $data = $this->request->only([
            'seller_id',
            'category_id',
            'item_condition_id',
            'city_id',
            'title',
            'description',
            'price',
            'status_id'
        ]);

        if (!trim($data['title']) || !trim($data['description']) || !trim($data['price'])) {
            $this->session->setFlash('error', 'Заполните обязательные поля');
            $this->redirect('/admin/advertisements/create');
            return;
        }

        $adRepo = new AdvertisementRepository();
        $ad = $adRepo->create([
            'seller_id' => (int) ($data['seller_id'] ?? 0),
            'category_id' => (int) ($data['category_id'] ?? 0),
            'item_condition_id' => (int) ($data['item_condition_id'] ?? 0),
            'city_id' => (int) ($data['city_id'] ?? 0),
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'price' => (float) ($data['price'] ?? 0),
            'status_id' => (int) ($data['status_id'] ?? 2),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->handleAdImageUpload($ad->ad_id);

        $this->session->setFlash('success', 'Объявление создано');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsEdit(int $id): void
    {
        $adRepo = new AdvertisementRepository();
        $ad = $adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $categories = $this->getCategoryTree();
        $cities = (new CityRepository())->findAll('name');
        $conditions = (new ItemConditionRepository())->findAll();
        $users = (new UserRepository())->findAllSimple();
        $statuses = $adRepo->getAllStatuses();
        $images = $adRepo->getImages($id);

        $this->renderAdmin('admin/advertisements/edit', [
            'title' => 'Редактировать объявление',
            'ad' => $ad,
            'categories' => $categories,
            'cities' => $cities,
            'conditions' => $conditions,
            'users' => $users,
            'statuses' => $statuses,
            'images' => $images,
        ]);
    }

    public function advertisementsUpdate(int $id): void
    {
        $adRepo = new AdvertisementRepository();
        $ad = $adRepo->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $data = $this->request->only([
            'seller_id',
            'category_id',
            'item_condition_id',
            'city_id',
            'title',
            'description',
            'price',
            'status_id'
        ]);

        $adRepo->update($id, [
            'seller_id' => (int) ($data['seller_id'] ?? 0),
            'category_id' => (int) ($data['category_id'] ?? 0),
            'item_condition_id' => (int) ($data['item_condition_id'] ?? 0),
            'city_id' => (int) ($data['city_id'] ?? 0),
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'price' => (float) ($data['price'] ?? 0),
            'status_id' => (int) ($data['status_id'] ?? 2),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $deleteImages = $this->request->post('delete_images');
        if (is_array($deleteImages)) {
            foreach ($deleteImages as $imageId) {
                $adRepo->deleteImage((int) $imageId);
            }
        }

        $this->handleAdImageUpload($id);

        $this->session->setFlash('success', 'Объявление обновлено');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsDelete(int $id): void
    {
        $adRepo = new AdvertisementRepository();
        $images = $adRepo->getImages($id);
        foreach ($images as $img) {
            $adRepo->deleteImage($img->image_id);
        }
        $adRepo->delete($id);
        $this->session->setFlash('success', 'Объявление удалено');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $adRepo = new AdvertisementRepository();
            foreach ($ids as $adId) {
                $images = $adRepo->getImages((int) $adId);
                foreach ($images as $img) {
                    $adRepo->deleteImage($img->image_id);
                }
            }
            $adRepo->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Объявления удалены');
        $this->redirect('/admin/advertisements');
    }

    // ─── Ad Chat Messages ────────────────────────────────

    public function adChatMessagesIndex(): void
    {
        $rows = (new ChatMessageRepository())->findAllWithDetails();
        $this->renderAdmin('admin/ad_chat_messages/index', [
            'title' => 'Сообщения чата',
            'items' => $rows,
        ]);
    }

    public function adChatMessagesCreate(): void
    {
        $users = (new UserRepository())->findAllSimple();
        $ads = (new AdvertisementRepository())->findAllSimple();
        $this->renderAdmin('admin/ad_chat_messages/create', [
            'title' => 'Создать сообщение',
            'users' => $users,
            'ads' => $ads,
        ]);
    }

    public function adChatMessagesStore(): void
    {
        $data = $this->request->only(['ad_id', 'sender_id', 'receiver_id', 'message']);
        if (!trim($data['message']) || !$data['ad_id'] || !$data['sender_id'] || !$data['receiver_id']) {
            $this->session->setFlash('error', 'Заполните все поля');
            $this->redirect('/admin/ad_chat_messages/create');
            return;
        }
        (new ChatMessageRepository())->create($data);
        $this->session->setFlash('success', 'Сообщение создано');
        $this->redirect('/admin/ad_chat_messages');
    }

    public function adChatMessagesEdit(int $id): void
    {
        $item = (new ChatMessageRepository())->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }
        $users = (new UserRepository())->findAllSimple();
        $ads = (new AdvertisementRepository())->findAllSimple();
        $this->renderAdmin('admin/ad_chat_messages/edit', [
            'title' => 'Редактировать сообщение',
            'item' => $item,
            'users' => $users,
            'ads' => $ads,
        ]);
    }

    public function adChatMessagesUpdate(int $id): void
    {
        $repo = new ChatMessageRepository();
        $item = $repo->findById($id);
        if (!$item) {
            Response::notFound();
            return;
        }

        $data = $this->request->only(['ad_id', 'sender_id', 'receiver_id', 'message']);
        if (!trim($data['message']) || !$data['ad_id'] || !$data['sender_id'] || !$data['receiver_id']) {
            $this->session->setFlash('error', 'Заполните все поля');
            $this->redirect("/admin/ad_chat_messages/{$id}/edit");
            return;
        }
        $repo->update($id, $data);
        $this->session->setFlash('success', 'Сообщение обновлено');
        $this->redirect('/admin/ad_chat_messages');
    }

    public function adChatMessagesDelete(int $id): void
    {
        (new ChatMessageRepository())->delete($id);
        $this->session->setFlash('success', 'Сообщение удалено');
        $this->redirect('/admin/ad_chat_messages');
    }

    public function adChatMessagesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            (new ChatMessageRepository())->batchDelete($ids);
        }
        $this->session->setFlash('success', 'Сообщения удалены');
        $this->redirect('/admin/ad_chat_messages');
    }

    // ─── Category tree helper ────────────────────────────

    private function getCategoryTree(): array
    {
        return (new CategoryRepository())->getTree();
    }

    // ─── Image upload helper ──────────────────────────────

    private function handleAdImageUpload(int $adId): void
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

        $adRepo = new AdvertisementRepository();
        $sortOrder = $adRepo->getNextSortOrder($adId);

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
                $adRepo->addImage($adId, 'uploads/' . $newName, $sortOrder++);
            }
        }
    }
}
