<?php

namespace App\Models;

class LocationCity extends \Model
{
    protected $table = 'location_city';
    protected $primaryKey = 'id';

    protected $connection = 'COMMON';

    protected $fillable = [
        'name',
        'orders',
        'japan_name',
        'area_name',
        'area_japan_name',
        'status',
        'slug',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
