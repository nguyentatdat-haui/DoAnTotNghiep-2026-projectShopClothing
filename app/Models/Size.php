<?php

namespace App\Models;

class Size extends \Model
{
    protected $table = 'sizes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
 
