<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class AuthController extends BaseController
{
    protected $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    /**
     * Show login form.
     */
    public function loginForm()
    {
        if (!empty($_SESSION['user_id'])) {
            return $this->redirect(rtrim(base_url(), '/') . '/');
        }
        return $this->view('auth/login', [
            'title' => 'Đăng nhập',
        ]);
    }

    /**
     * Process login.
     */
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error('Vui lòng nhập email và mật khẩu.');
            return $this->redirect(rtrim(base_url(), '/') . '/login');
        }

        $user = $this->userRepository->findBy('email', $email);
        if (!$user) {
            $this->error('Email hoặc mật khẩu không đúng.');
            return $this->redirect(rtrim(base_url(), '/') . '/login');
        }

        $hash = is_object($user) ? ($user->password ?? '') : ($user['password'] ?? '');
        if (!password_verify($password, $hash)) {
            // Check legacy MD5 if needed (based on Admin AuthController logic)
            if (strlen($hash) === 32 && ctype_xdigit($hash)) {
                if (md5($password) !== $hash) {
                    $this->error('Email hoặc mật khẩu không đúng.');
                    return $this->redirect(rtrim(base_url(), '/') . '/login');
                }
            } else {
                $this->error('Email hoặc mật khẩu không đúng.');
                return $this->redirect(rtrim(base_url(), '/') . '/login');
            }
        }

        $userId = is_object($user) ? $user->id : $user['id'];
        $userName = is_object($user) ? $user->name : $user['name'];

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_email'] = $email;

        $this->success('Đăng nhập thành công.');
        return $this->redirect(rtrim(base_url(), '/') . '/');
    }

    /**
     * Show register form.
     */
    public function registerForm()
    {
        if (!empty($_SESSION['user_id'])) {
            return $this->redirect(rtrim(base_url(), '/') . '/');
        }
        return $this->view('auth/register', [
            'title' => 'Đăng ký tài khoản',
        ]);
    }

    /**
     * Process registration.
     */
    public function register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->error('Vui lòng điền đầy đủ các thông tin bắt buộc.');
            return $this->redirect(rtrim(base_url(), '/') . '/register');
        }

        if ($password !== $confirm_password) {
            $this->error('Mật khẩu xác nhận không khớp.');
            return $this->redirect(rtrim(base_url(), '/') . '/register');
        }

        $existingUser = $this->userRepository->findBy('email', $email);
        if ($existingUser) {
            $this->error('Email này đã được sử dụng.');
            return $this->redirect(rtrim(base_url(), '/') . '/register');
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'address' => $address,
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userRepository->create($userData);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $this->success('Đăng ký tài khoản thành công.');
            return $this->redirect(rtrim(base_url(), '/') . '/');
        } else {
            $this->error('Có lỗi xảy ra trong quá trình đăng ký.');
            return $this->redirect(rtrim(base_url(), '/') . '/register');
        }
    }

    /**
     * Logout.
     */
    public function logout()
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
        $this->info('Bạn đã đăng xuất.');
        return $this->redirect(rtrim(base_url(), '/') . '/');
    }
}
