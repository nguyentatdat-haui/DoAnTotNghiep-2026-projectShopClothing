<?php

namespace App\Models;

class Location extends \Model
{
    protected $table = 'location';
    protected $primaryKey = 'id';

    protected $connection = 'COMMON';

    protected $fillable = [
        'parent_id',
        'name',
        'raw_name',
        'japan_name',
        'iframe_url',
        'list_url',
        'status',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}


