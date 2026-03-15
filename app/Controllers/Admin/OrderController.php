<?php

namespace App\Controllers\Admin;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

class OrderController extends BaseAdminController
{
    protected $orderRepository;
    protected $orderItemRepository;

    public function __construct()
    {
        parent::__construct();
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    public function index()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $result = $this->orderRepository->getAllForAdmin($page, 20);
        return $this->view('admin/orders/index', [
            'title' => 'Đơn hàng',
            'current_page' => 'orders',
            'orders' => $result['data'],
            'pagination' => $result,
        ]);
    }

    public function show($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            $this->error('Đơn hàng không tồn tại.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/orders');
        }
        $items = $this->orderItemRepository->getByOrderId($id);
        $user = null;
        if (!empty($order->user_id)) {
            $userRepo = new \App\Repositories\UserRepository();
            $user = $userRepo->findById($order->user_id);
        }
        return $this->view('admin/orders/show', [
            'title' => 'Chi tiết đơn #' . $id,
            'current_page' => 'orders',
            'order' => $order,
            'items' => $items,
            'user' => $user,
        ]);
    }

    public function updateStatus($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            $this->error('Đơn hàng không tồn tại.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/orders');
        }
        $status = trim($_POST['status'] ?? '');
        $allowed = ['pending', 'processing', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->error('Trạng thái không hợp lệ.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/orders/' . $id);
        }
        $this->orderRepository->update($id, ['status' => $status]);
        $this->success('Đã cập nhật trạng thái đơn hàng.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/orders/' . $id);
    }
}
