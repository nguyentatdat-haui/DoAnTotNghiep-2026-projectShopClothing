<?php

namespace App\Models;

class Order extends \Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'created_at',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}

