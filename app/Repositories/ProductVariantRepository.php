<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantRepository extends BaseRepository
{
    protected $model = ProductVariant::class;

    public function getByProductId($productId)
    {
        $sql = "SELECT v.*, c.name as color_name, c.code as color_code, s.name as size_name 
                FROM {$this->table} v 
                LEFT JOIN colors c ON c.id = v.color_id 
                LEFT JOIN sizes s ON s.id = v.size_id 
                WHERE v.product_id = :product_id 
                ORDER BY v.id";
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }

    public function deleteByProductId($productId)
    {
        return $this->db->delete($this->table, 'product_id = :product_id', ['product_id' => $productId]);
    }

    public function addVariant($productId, $colorId, $sizeId, $price = null, $stockQuantity = null, $sku = '')
    {
        $priceVal = ($price !== null && $price !== '') ? (float) $price : 0;
        $stockVal = ($stockQuantity !== null && $stockQuantity !== '') ? (int) $stockQuantity : null;
        $skuTrim = $sku !== null ? trim((string) $sku) : '';
        if ($skuTrim === '') {
            $skuTrim = 'PV-' . $productId . '-' . ($colorId ?: 0) . '-' . ($sizeId ?: 0) . '-' . uniqid();
        }
        $data = [
            'product_id' => $productId,
            'color_id' => $colorId ?: null,
            'size_id' => $sizeId ?: null,
            'price' => $priceVal,
            'stock_quantity' => $stockVal,
            'sku' => $skuTrim,
        ];
        $id = $this->db->insert($this->table, $data);
        return $id ? $this->findById($id) : null;
    }
}
