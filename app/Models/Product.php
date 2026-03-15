<?php

namespace App\Models;

class Product extends \Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'category_id',
        'name',
        'description',
        'base_price',
        'discount_price',
        'thumbnail',
        'images',
        'is_new',
        'is_best_seller',
        'status',
        'created_at',
        'updated_at',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
 
