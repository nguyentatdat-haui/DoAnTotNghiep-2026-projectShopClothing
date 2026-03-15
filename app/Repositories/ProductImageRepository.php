<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository extends BaseRepository
{
    protected $model = ProductImage::class;

    public function getByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY is_main DESC, id ASC";
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }

    public function deleteByProductId($productId)
    {
        return $this->db->delete($this->table, 'product_id = :product_id', ['product_id' => $productId]);
    }

    public function setAllNotMain($productId)
    {
        $sql = "UPDATE {$this->table} SET is_main = 0 WHERE product_id = :product_id";
        return $this->db->query($sql, ['product_id' => $productId]);
    }

    /** Thêm một ảnh (không bắt buộc updated_at). */
    public function addImage($productId, $imageUrl, $isMain = 0)
    {
        $data = [
            'product_id' => $productId,
            'image_url' => $imageUrl,
            'is_main' => $isMain ? 1 : 0,
        ];
        $id = $this->db->insert($this->table, $data);
        return $id ? $this->findById($id) : null;
    }

}