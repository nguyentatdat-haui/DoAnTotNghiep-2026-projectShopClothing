<?php

namespace App\Models;

class ProductVariant extends \Model
{
    protected $table = 'product_variants';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'product_id',
        'color_id',
        'size_id',
        'price',
        'stock_quantity',
        'sku',
        'created_at',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
 
