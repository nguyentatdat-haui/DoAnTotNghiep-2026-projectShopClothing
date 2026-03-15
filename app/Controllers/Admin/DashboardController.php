<?php

namespace App\Controllers\Admin;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $productRepo = new ProductRepository();
        $orderRepo = new OrderRepository();
        $totalProducts = $productRepo->count();
        $totalOrders = $orderRepo->count();

        return $this->view('admin/dashboard', [
            'title' => 'Dashboard',
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
        ]);
    }
}
