<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
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
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    // ─── Dashboard ────────────────────────────────────────

    public function dashboard(): void
    {
        $stats = [
            'users' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM users")['cnt'],
            'ads' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM advertisements")['cnt'],
            'cities' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM cities")['cnt'],
            'categories' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM categories")['cnt'],
            'conditions' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM item_conditions")['cnt'],
            'messages' => $this->db->fetch("SELECT COUNT(*) AS cnt FROM ad_chat_messages")['cnt'],
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
        $this->db->insert('cities', ['name' => $name]);
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
        $this->db->update('cities', ['name' => $name], 'city_id = ?', [$id]);
        $this->session->setFlash('success', 'Город обновлён');
        $this->redirect('/admin/cities');
    }

    public function citiesDelete(int $id): void
    {
        $this->db->delete('cities', 'city_id = ?', [$id]);
        $this->session->setFlash('success', 'Город удалён');
        $this->redirect('/admin/cities');
    }

    public function citiesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('cities', "city_id IN ({$placeholders})", $ids);
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

        $this->db->insert('categories', $data);
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

        $this->db->update('categories', $data, 'category_id = ?', [$id]);
        $this->session->setFlash('success', 'Категория обновлена');
        $this->redirect('/admin/categories');
    }

    public function categoriesDelete(int $id): void
    {
        $this->db->delete('categories', 'category_id = ?', [$id]);
        $this->session->setFlash('success', 'Категория удалена');
        $this->redirect('/admin/categories');
    }

    public function categoriesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('categories', "category_id IN ({$placeholders})", $ids);
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
        $this->db->insert('item_conditions', ['name' => $name]);
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
        $this->db->update('item_conditions', ['name' => $name], 'item_condition_id = ?', [$id]);
        $this->session->setFlash('success', 'Состояние обновлено');
        $this->redirect('/admin/item_conditions');
    }

    public function itemConditionsDelete(int $id): void
    {
        $this->db->delete('item_conditions', 'item_condition_id = ?', [$id]);
        $this->session->setFlash('success', 'Состояние удалено');
        $this->redirect('/admin/item_conditions');
    }

    public function itemConditionsBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('item_conditions', "item_condition_id IN ({$placeholders})", $ids);
        }
        $this->session->setFlash('success', 'Состояния удалены');
        $this->redirect('/admin/item_conditions');
    }

    // ─── Users ───────────────────────────────────────────

    public function usersIndex(): void
    {
        $rows = $this->db->fetchAll(
            "SELECT u.*, r.name AS role_name, c.name AS city_name
             FROM users u
             JOIN roles r ON u.role_id = r.role_id
             JOIN cities c ON u.city_id = c.city_id
             ORDER BY u.user_id ASC"
        );
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

        $existing = $this->db->fetch("SELECT user_id FROM users WHERE email = ?", [trim($data['email'])]);
        if ($existing) {
            $this->session->setFlash('error', 'Пользователь с таким email уже существует');
            $this->redirect('/admin/users/create');
            return;
        }

        $this->db->insert('users', [
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

        $this->db->update('users', $updateData, 'user_id = ?', [$id]);
        $this->session->setFlash('success', 'Пользователь обновлён');
        $this->redirect('/admin/users');
    }

    public function usersDelete(int $id): void
    {
        $this->db->delete('users', 'user_id = ?', [$id]);
        $this->session->setFlash('success', 'Пользователь удалён');
        $this->redirect('/admin/users');
    }

    public function usersBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('users', "user_id IN ({$placeholders})", $ids);
        }
        $this->session->setFlash('success', 'Пользователи удалены');
        $this->redirect('/admin/users');
    }

    // ─── Advertisements ──────────────────────────────────

    public function advertisementsIndex(): void
    {
        $rows = $this->db->fetchAll(
            "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                    ic.name AS condition_name, s.name AS status_name,
                    u.email AS seller_email, u.first_name AS seller_first_name, u.last_name AS seller_last_name
             FROM advertisements a
             JOIN categories c ON a.category_id = c.category_id
             JOIN cities ct ON a.city_id = ct.city_id
             JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
             JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
             JOIN users u ON a.seller_id = u.user_id
             ORDER BY a.created_at DESC"
        );
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
        $users = $this->db->fetchAll("SELECT user_id, email, first_name, last_name FROM users ORDER BY user_id ASC");

        $statuses = $this->db->fetchAll("SELECT * FROM advertisement_statuses ORDER BY ad_status_id ASC");

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

        $adId = $this->db->insert('advertisements', [
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

        $this->handleAdImageUpload($adId);

        $this->session->setFlash('success', 'Объявление создано');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsEdit(int $id): void
    {
        $ad = (new AdvertisementRepository())->findById($id);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $categories = $this->getCategoryTree();
        $cities = (new CityRepository())->findAll('name');
        $conditions = (new ItemConditionRepository())->findAll();
        $users = $this->db->fetchAll("SELECT user_id, email, first_name, last_name FROM users ORDER BY user_id ASC");
        $statuses = $this->db->fetchAll("SELECT * FROM advertisement_statuses ORDER BY ad_status_id ASC");
        $images = (new AdvertisementRepository())->getImages($id);

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
        $ad = (new AdvertisementRepository())->findById($id);
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

        $this->db->update('advertisements', [
            'seller_id' => (int) ($data['seller_id'] ?? 0),
            'category_id' => (int) ($data['category_id'] ?? 0),
            'item_condition_id' => (int) ($data['item_condition_id'] ?? 0),
            'city_id' => (int) ($data['city_id'] ?? 0),
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'price' => (float) ($data['price'] ?? 0),
            'status_id' => (int) ($data['status_id'] ?? 2),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'ad_id = ?', [$id]);

        $deleteImages = $this->request->post('delete_images');
        if (is_array($deleteImages)) {
            foreach ($deleteImages as $imageId) {
                (new AdvertisementRepository())->deleteImage((int) $imageId);
            }
        }

        $this->handleAdImageUpload($id);

        $this->session->setFlash('success', 'Объявление обновлено');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsDelete(int $id): void
    {
        $images = (new AdvertisementRepository())->getImages($id);
        foreach ($images as $img) {
            (new AdvertisementRepository())->deleteImage($img->image_id);
        }
        $this->db->delete('advertisements', 'ad_id = ?', [$id]);
        $this->session->setFlash('success', 'Объявление удалено');
        $this->redirect('/admin/advertisements');
    }

    public function advertisementsBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            foreach ($ids as $adId) {
                $images = (new AdvertisementRepository())->getImages((int) $adId);
                foreach ($images as $img) {
                    (new AdvertisementRepository())->deleteImage($img->image_id);
                }
            }
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('advertisements', "ad_id IN ({$placeholders})", $ids);
        }
        $this->session->setFlash('success', 'Объявления удалены');
        $this->redirect('/admin/advertisements');
    }

    // ─── Ad Chat Messages ────────────────────────────────

    public function adChatMessagesIndex(): void
    {
        $rows = $this->db->fetchAll(
            "SELECT m.*, 
                    s.email AS sender_email, s.first_name AS sender_first_name, s.last_name AS sender_last_name,
                    r.email AS receiver_email, r.first_name AS receiver_first_name, r.last_name AS receiver_last_name,
                    a.title AS ad_title
             FROM ad_chat_messages m
             JOIN users s ON m.sender_id = s.user_id
             JOIN users r ON m.receiver_id = r.user_id
             JOIN advertisements a ON m.ad_id = a.ad_id
             ORDER BY m.ad_chat_message_id ASC"
        );
        $this->renderAdmin('admin/ad_chat_messages/index', [
            'title' => 'Сообщения чата',
            'items' => $rows,
        ]);
    }

    public function adChatMessagesCreate(): void
    {
        $users = $this->db->fetchAll("SELECT user_id, email, first_name, last_name FROM users ORDER BY user_id ASC");
        $ads = $this->db->fetchAll("SELECT ad_id, title FROM advertisements ORDER BY ad_id DESC");
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
        $this->db->insert('ad_chat_messages', $data);
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
        $users = $this->db->fetchAll("SELECT user_id, email, first_name, last_name FROM users ORDER BY user_id ASC");
        $ads = $this->db->fetchAll("SELECT ad_id, title FROM advertisements ORDER BY ad_id DESC");
        $this->renderAdmin('admin/ad_chat_messages/edit', [
            'title' => 'Редактировать сообщение',
            'item' => $item,
            'users' => $users,
            'ads' => $ads,
        ]);
    }

    public function adChatMessagesUpdate(int $id): void
    {
        $item = (new ChatMessageRepository())->findById($id);
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
        $this->db->update('ad_chat_messages', $data, 'ad_chat_message_id = ?', [$id]);
        $this->session->setFlash('success', 'Сообщение обновлено');
        $this->redirect('/admin/ad_chat_messages');
    }

    public function adChatMessagesDelete(int $id): void
    {
        $this->db->delete('ad_chat_messages', 'ad_chat_message_id = ?', [$id]);
        $this->session->setFlash('success', 'Сообщение удалено');
        $this->redirect('/admin/ad_chat_messages');
    }

    public function adChatMessagesBatchDelete(): void
    {
        $ids = $this->request->post('ids');
        if (is_array($ids) && count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->delete('ad_chat_messages', "ad_chat_message_id IN ({$placeholders})", $ids);
        }
        $this->session->setFlash('success', 'Сообщения удалены');
        $this->redirect('/admin/ad_chat_messages');
    }

    // ─── Category tree helper ────────────────────────────

    private function getCategoryTree(): array
    {
        $repo = new CategoryRepository();
        $parents = $repo->findParents();
        $tree = [];
        foreach ($parents as $parent) {
            $tree[] = [
                'parent' => $parent,
                'children' => $repo->findByParentId($parent->category_id),
            ];
        }
        return $tree;
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

        $sortOrder = (new AdvertisementRepository())->getNextSortOrder($adId);

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
                (new AdvertisementRepository())->addImage($adId, 'uploads/' . $newName, $sortOrder++);
            }
        }
    }
}
