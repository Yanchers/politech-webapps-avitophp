<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\CityRepository;

class AuthController extends Controller
{
    private UserRepository $userRepo;
    private RoleRepository $roleRepo;
    private CityRepository $cityRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepository();
        $this->roleRepo = new RoleRepository();
        $this->cityRepo = new CityRepository();
    }

    public function loginForm(): void
    {
        $this->session->remove('old_input');
        $this->render('auth/login', [
            'title' => 'Вход',
        ]);
    }

    public function login(): void
    {
        $email = $this->request->post('email');
        $password = $this->request->post('password');

        $validator = new Validator();
        if (!$validator->validate(['email' => $email, 'password' => $password], [
            'email' => 'required|email',
            'password' => 'required',
        ])) {
            $this->session->setFlash('error', $validator->getFirstError());
            $this->session->set('old_input', ['email' => $email]);
            $this->back();
            return;
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            $this->session->setFlash('error', 'Неверный email или пароль');
            $this->session->set('old_input', ['email' => $email]);
            $this->back();
            return;
        }

        $role = $this->roleRepo->findById($user->role_id);
        $this->session->login($user->user_id, $role->name);
        $this->session->setFlash('success', 'Добро пожаловать, ' . $user->first_name . '!');
        $this->redirect('/');
    }

    public function registerForm(): void
    {
        $cities = $this->cityRepo->findAll('name');
        $this->render('auth/register', [
            'title' => 'Регистрация',
            'cities' => $cities,
        ]);
    }

    public function register(): void
    {
        $data = $this->request->only(['email', 'phone', 'password', 'first_name', 'last_name', 'city_id']);

        $validator = new Validator();
        if (!$validator->validate($data, [
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|min:6|confirmed',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'city_id' => 'required|numeric',
        ])) {
            $this->session->setFlash('error', $validator->getFirstError());
            $this->session->set('old_input', $data);
            $this->back();
            return;
        }

        $user = $this->userRepo->create([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role_id' => 1,
            'city_id' => (int) $data['city_id'],
        ]);

        $role = $this->roleRepo->findById(1);
        $this->session->login($user->user_id, $role->name);
        $this->session->setFlash('success', 'Регистрация прошла успешно!');
        $this->session->remove('old_input'); // очистим old_input чтобы не мешало в других формах
        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->session->logout();
        $this->session->setFlash('success', 'Вы вышли из системы');
        $this->redirect('/');
    }
}
