<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

abstract class BaseAdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['_layout'] = 'layouts/admin';
    }

    /**
     * Ensure user is logged in as admin. Call in each action that requires auth.
     * Redirects to /admin/login if not authenticated.
     */
    protected function requireAdmin()
    {
        if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
            $this->flashMessage('Vui lòng đăng nhập.', 'warning');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/login');
        }
        return null;
    }

    /**
     * Get current admin user id (or null).
     */
    protected function adminId()
    {
        return $_SESSION['admin_id'] ?? null;
    }
}
