<?php

namespace App\Models;

class LocationDistrict extends \Model
{
    protected $table = 'location_district';
    protected $primaryKey = 'id';

    protected $connection = 'COMMON';

    protected $fillable = [
        'parent_id',
        'name',
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
