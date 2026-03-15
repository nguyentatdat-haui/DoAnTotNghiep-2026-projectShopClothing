<?php

namespace App\Models;

class OrderItem extends \Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}

