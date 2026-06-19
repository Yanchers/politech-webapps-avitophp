<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Validator;
use App\Repositories\ChatMessageRepository;
use App\Repositories\AdvertisementRepository;
use App\Repositories\UserRepository;

class ChatController extends Controller
{
    private ChatMessageRepository $chatRepo;
    private AdvertisementRepository $adRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->chatRepo = new ChatMessageRepository();
        $this->adRepo = new AdvertisementRepository();
        $this->userRepo = new UserRepository();
    }

    public function index(): void
    {
        $userId = $this->session->getUserId();
        $chats = $this->chatRepo->getUserChats($userId);

        $this->render('chat/index', [
            'title' => 'Мои сообщения',
            'chats' => $chats,
        ]);
    }

    public function show(int $adId, int $userId): void
    {
        $currentUserId = $this->session->getUserId();

        $ad = $this->adRepo->findById($adId);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $otherUser = $this->userRepo->findById($userId);
        if (!$otherUser) {
            Response::notFound();
            return;
        }

        if ($userId !== $ad->seller_id && $currentUserId !== $ad->seller_id) {
            Response::forbidden();
            return;
        }

        if ($currentUserId === $userId) {
            $this->redirect('/chat');
            return;
        }

        $messages = $this->chatRepo->findByAdAndUsers($adId, $currentUserId, $userId);

        $this->render('chat/show', [
            'title' => 'Чат: ' . $ad->title,
            'ad' => $ad,
            'otherUser' => $otherUser,
            'messages' => $messages,
        ]);
    }

    public function store(int $adId, int $userId): void
    {
        $currentUserId = $this->session->getUserId();

        $ad = $this->adRepo->findById($adId);
        if (!$ad) {
            Response::notFound();
            return;
        }

        $otherUser = $this->userRepo->findById($userId);
        if (!$otherUser) {
            Response::notFound();
            return;
        }

        if ($userId !== $ad->seller_id && $currentUserId !== $ad->seller_id) {
            Response::forbidden();
            return;
        }

        if ($currentUserId === $userId) {
            $this->redirect('/chat');
            return;
        }

        $message = $this->request->post('message');
        $validator = new Validator();
        if (!$validator->validate(['message' => $message], ['message' => 'required|min:1'])) {
            $this->session->setFlash('error', 'Сообщение не может быть пустым');
            $this->redirect('/chat/' . $adId . '/' . $userId);
            return;
        }

        $this->chatRepo->create([
            'ad_id' => $adId,
            'sender_id' => $currentUserId,
            'receiver_id' => $userId,
            'message' => $message,
        ]);

        $this->redirect('/chat/' . $adId . '/' . $userId);
    }
}
