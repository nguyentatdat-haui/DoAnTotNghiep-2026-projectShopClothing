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
        
        // Basic counts
        $totalProducts = $productRepo->count();
        $totalOrders = $orderRepo->count();
        
        // Additional stats
        $db = \Database::getInstance();
        
        // Customer count
        $stmtUsers = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        $totalUsers = $stmtUsers->fetch()['count'] ?? 0;
        
        // Total Revenue
        $stmtRevenue = $db->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'");
        $totalRevenue = $stmtRevenue->fetch()['revenue'] ?? 0;
        
        // Latest Orders
        $stmtRecentOrders = $db->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
        $recentOrders = $stmtRecentOrders->fetchAll();

        return $this->view('admin/dashboard', [
            'title' => 'Bảng điều khiển',
            'current_page' => 'dashboard',
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'total_revenue' => $totalRevenue,
            'recent_orders' => $recentOrders
        ]);
    }
}
