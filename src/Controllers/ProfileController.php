<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Repositories\CityRepository;
use App\Repositories\AdvertisementRepository;
use App\Core\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = $this->session->getUser();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $userRepo = new UserRepository();
        $cityRepo = new CityRepository();
        $adRepo = new AdvertisementRepository();

        $userModel = $userRepo->findById($user['user_id']);
        $city = $userModel->city_id ? $cityRepo->findById($userModel->city_id) : null;
        $adsCount = count($adRepo->findBySellerId($user['user_id']));

        $this->render('profile/profile', [
            'title' => 'Профиль пользователя',
            'user' => $user,
            'userModel' => $userModel,
            'city' => $city,
            'adsCount' => $adsCount,
        ]);
    }

    public function edit()
    {
        $user = $this->session->getUser();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $userRepo = new UserRepository();
        $cityRepo = new CityRepository();

        $userModel = $userRepo->findById($user['user_id']);
        $cities = $cityRepo->findAll();

        $this->render('profile/edit', [
            'title' => 'Редактирование профиля',
            'user' => $user,
            'userModel' => $userModel,
            'cities' => $cities,
        ]);
    }

    public function update()
    {
        $user = $this->session->getUser();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $data = $this->request->only(['first_name', 'last_name', 'phone', 'city_id', 'password', 'password_confirmation']);

        $rules = [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'phone'      => 'required|max:20',
            'city_id'    => 'required|numeric',
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'min:6|confirmed';
        }

        $validator = new Validator();
        if (!$validator->validate($data, $rules)) {
            $this->session->setFlash('error', $validator->getFirstError());
            $this->back();
            return;
        }

        $updateData = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'],
            'city_id'    => (int) $data['city_id'],
        ];

        if (!empty($data['password'])) {
            $updateData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $userRepo = new UserRepository();
        $userRepo->update($user['user_id'], $updateData);

        $this->session->setFlash('success', 'Профиль успешно обновлён');
        $this->redirect('/profile');
    }
}
