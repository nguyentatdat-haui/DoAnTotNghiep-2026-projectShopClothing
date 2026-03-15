<?php

namespace App\Models;

class ProductImage extends \Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'product_id',
        'image_url',
        'is_main',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
 
