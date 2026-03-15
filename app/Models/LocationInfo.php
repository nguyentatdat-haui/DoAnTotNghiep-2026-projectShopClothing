<?php

namespace App\Models;

class LocationInfo extends \Model
{
    protected $table = 'location_info';
    protected $primaryKey = 'id';

    protected $connection = 'COMMON';

    protected $fillable = [
        'city_id',
        'district_id',
        'title',
        'description',
        'reason_title',
        'reason_content',
        'feature_title',
        'feature_description',
        'feature_content',
        'case_title',
        'case_content',
        'qa_title',
        'qa_content'
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}


