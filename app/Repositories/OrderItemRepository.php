<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
    protected $model = OrderItem::class;

    /** @var bool|null */
    private static $hasProductIdColumn;

    /**
     * Get all items for an order (raw rows for admin detail).
     * Works with or without order_items.product_id column (no-variant items).
     */
    public function getByOrderId($orderId)
    {
        $hasProductId = $this->hasOrderItemsProductIdColumn();
        if ($hasProductId) {
            $sql = "SELECT oi.*, 
                    COALESCE(p_from_variant.name, p_direct.name) AS product_name, 
                    pv.sku 
                    FROM {$this->table} oi 
                    LEFT JOIN product_variants pv ON pv.id = oi.product_variant_id 
                    LEFT JOIN products p_from_variant ON p_from_variant.id = pv.product_id 
                    LEFT JOIN products p_direct ON p_direct.id = oi.product_id 
                    WHERE oi.order_id = :order_id 
                    ORDER BY oi.id";
        } else {
            $sql = "SELECT oi.*, 
                    p_from_variant.name AS product_name, 
                    pv.sku 
                    FROM {$this->table} oi 
                    LEFT JOIN product_variants pv ON pv.id = oi.product_variant_id 
                    LEFT JOIN products p_from_variant ON p_from_variant.id = pv.product_id 
                    WHERE oi.order_id = :order_id 
                    ORDER BY oi.id";
        }
        return $this->db->fetchAll($sql, ['order_id' => $orderId]);
    }

    /**
     * Whether order_items table has product_id column (for no-variant items).
     * Public so OrderController can avoid inserting product_id when column is missing.
     */
    public function orderItemsHaveProductIdColumn(): bool
    {
        return $this->hasOrderItemsProductIdColumn();
    }

    /**
     * Check if order_items table has product_id column (for no-variant items).
     */
    private function hasOrderItemsProductIdColumn(): bool
    {
        if (self::$hasProductIdColumn !== null) {
            return self::$hasProductIdColumn;
        }
        try {
            $row = $this->db->fetch("SHOW COLUMNS FROM {$this->table} LIKE 'product_id'", []);
            self::$hasProductIdColumn = !empty($row);
        } catch (\Throwable $e) {
            self::$hasProductIdColumn = false;
        }
        return self::$hasProductIdColumn;
    }
}
