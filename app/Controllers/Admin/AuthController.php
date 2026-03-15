<?php

namespace App\Controllers\Admin;

use App\Repositories\UserRepository;

class AuthController extends BaseAdminController
{
    protected $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    /**
     * Show login form. If already logged in, redirect to dashboard.
     */
    public function loginForm()
    {
        if (!empty($_SESSION['admin_id'])) {
            return $this->redirect(rtrim(base_url(), '/') . '/admin');
        }
        return $this->view('admin/login', [
            'title' => 'Đăng nhập Admin',
            'show_sidebar' => false,
        ]);
    }

    /**
     * Process login: email + password, role must be 'admin'.
     */
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->error('Vui lòng nhập email và mật khẩu.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
        }

        $user = $this->userRepository->findBy('email', $email);
        if (!$user) {
            $this->error('Email hoặc mật khẩu không đúng.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
        }

        $role = is_object($user) ? ($user->role ?? null) : ($user['role'] ?? null);
        if (strtolower((string)$role) !== 'admin') {
            $this->error('Bạn không có quyền truy cập.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
        }

        $hash = is_object($user) ? ($user->password ?? '') : ($user['password'] ?? '');
        $passwordOk = false;
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            // Hash lưu dạng MD5 (32 ký tự hex) → so sánh md5(password)
            $passwordOk = (md5($password) === $hash);
        } else {
            // Hash bcrypt (password_hash) → dùng password_verify
            $passwordOk = password_verify($password, $hash);
        }
        if (!$passwordOk) {
            $this->error('Email hoặc mật khẩu không đúng.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
        }

        $userId = is_object($user) ? $user->id : $user['id'];
        $_SESSION['admin_id'] = $userId;
        $_SESSION['admin_email'] = $email;
        $this->success('Đăng nhập thành công.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin');
    }

    /**
     * Logout and redirect to login.
     */
    public function logout()
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_email']);
        $this->info('Bạn đã đăng xuất.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
    }
}
