<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    protected $model = Product::class;

    /**
     * Lấy sản phẩm cho trang danh sách (có phân trang, lọc theo danh mục).
     */
    public function getProductsForShop($page = 1, $perPage = 12, $categoryId = null)
    {
        $offset = ($page - 1) * $perPage;
        $where = 'WHERE 1=1';
        $params = ['limit' => $perPage, 'offset' => $offset];
        if ($categoryId) {
            $where .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON c.id = p.category_id 
                {$where} 
                ORDER BY p.id DESC 
                LIMIT :limit OFFSET :offset";
        $rows = $this->db->fetchAll($sql, $params);
        $items = array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $rows ?: []);
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p {$where}";
        unset($params['limit'], $params['offset']);
        $total = (int) ($this->db->fetch($countSql, $params)['total'] ?? 0);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;
        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];
    }

    /**
     * Search products by keyword (name, description).
     */
    public function searchByKeyword($keyword, $page = 1, $perPage = 12)
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->getProductsForShop($page, $perPage, null);
        }
        $offset = ($page - 1) * $perPage;
        $where = "WHERE (p.name LIKE :q OR p.description LIKE :q2)";
        $params = [
            'q' => '%' . $keyword . '%',
            'q2' => '%' . $keyword . '%',
            'limit' => $perPage,
            'offset' => $offset,
        ];
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON c.id = p.category_id 
                {$where} 
                ORDER BY p.id DESC 
                LIMIT :limit OFFSET :offset";
        $rows = $this->db->fetchAll($sql, $params);
        $items = array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $rows ?: []);
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p {$where}";
        unset($params['limit'], $params['offset']);
        $total = (int) ($this->db->fetch($countSql, $params)['total'] ?? 0);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;
        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];
    }

    /**
     * Get products on sale (discount_price set and less than base_price).
     */
    public function getProductsOnSale($page = 1, $perPage = 12)
    {
        $offset = ($page - 1) * $perPage;
        $where = "WHERE p.discount_price IS NOT NULL AND p.discount_price > 0 AND (p.base_price IS NULL OR p.discount_price < p.base_price)";
        $params = ['limit' => $perPage, 'offset' => $offset];
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON c.id = p.category_id 
                {$where} 
                ORDER BY p.id DESC 
                LIMIT :limit OFFSET :offset";
        $rows = $this->db->fetchAll($sql, $params);
        $items = array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $rows ?: []);
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p {$where}";
        unset($params['limit'], $params['offset']);
        $total = (int) ($this->db->fetch($countSql, $params)['total'] ?? 0);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;
        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];
    }

    /**
     * Get best sellers from orders: products with total quantity sold >= 2.
     * Uses order_items -> product_variants -> products (only items with product_variant_id > 0).
     */
    public function getBestSellersFromOrders($limit = 8)
    {
        $oiTable = (new \App\Models\OrderItem())->getTable();
        $pvTable = (new \App\Models\ProductVariant())->getTable();
        $sql = "SELECT pv.product_id, SUM(oi.quantity) as total_sold 
                FROM {$oiTable} oi 
                INNER JOIN {$pvTable} pv ON pv.id = oi.product_variant_id 
                WHERE oi.product_variant_id > 0 
                GROUP BY pv.product_id 
                HAVING total_sold >= 2 
                ORDER BY total_sold DESC 
                LIMIT " . (int) $limit;
        $rows = $this->db->fetchAll($sql);
        if (empty($rows)) {
            return [];
        }
        $ids = array_map('intval', array_column($rows, 'product_id'));
        $placeholders = implode(',', $ids);
        $orderField = 'FIELD(p.id, ' . $placeholders . ')';
        $sql2 = "SELECT p.*, c.name as category_name 
                 FROM {$this->table} p 
                 LEFT JOIN categories c ON c.id = p.category_id 
                 WHERE p.id IN ({$placeholders}) 
                 ORDER BY {$orderField}";
        $productRows = $this->db->fetchAll($sql2);
        return array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $productRows ?: []);
    }

    /**
     * Get latest products (for home: new arrivals, etc.).
     */
    public function getLatest($limit = 8, $offset = 0)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON c.id = p.category_id 
                ORDER BY p.id DESC 
                LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
        $rows = $this->db->fetchAll($sql);
        return array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $rows ?: []);
    }

    /**
     * Get 1 product by ID with images and variants.
     */
    public function getByIdWithDetails($id)
    {
        $product = $this->findById($id);
        if (!$product) {
            return null;
        }
        $imgTable = (new \App\Models\ProductImage())->getTable();
        $imgSql = "SELECT * FROM {$imgTable} WHERE product_id = :product_id ORDER BY is_main DESC, id ASC";
        $images = $this->db->fetchAll($imgSql, ['product_id' => $id]);
        $varTable = (new \App\Models\ProductVariant())->getTable();
        $varSql = "SELECT v.*, c.name as color_name, c.code as color_code, s.name as size_name 
                   FROM {$varTable} v 
                   LEFT JOIN colors c ON c.id = v.color_id 
                   LEFT JOIN sizes s ON s.id = v.size_id 
                   WHERE v.product_id = :product_id AND (v.stock_quantity IS NULL OR v.stock_quantity > 0)
                   ORDER BY v.id";
        $variants = $this->db->fetchAll($varSql, ['product_id' => $id]);
        $product->images = $images ?: [];
        $product->variants = $variants ?: [];
        return $product;
    }
}
