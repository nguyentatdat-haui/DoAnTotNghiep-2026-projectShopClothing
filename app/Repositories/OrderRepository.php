<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    protected $model = Order::class;

    /**
     * Create order without updated_at (orders table may not have it).
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $this->db->insert($this->table, $data);
        return $id ? $this->findById($id) : null;
    }

    /**
     * Get orders for admin list with user name/email.
     */
    public function getAllForAdmin($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $userTable = (new \App\Models\User())->getTable();
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} o 
                LEFT JOIN {$userTable} u ON u.id = o.user_id 
                ORDER BY o.id DESC 
                LIMIT :limit OFFSET :offset";
        $rows = $this->db->fetchAll($sql, ['limit' => $perPage, 'offset' => $offset]);
        $total = (int) $this->db->fetch("SELECT COUNT(*) as c FROM {$this->table}")['c'];
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;
        return [
            'data' => $rows,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Get orders by User ID.
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
}
