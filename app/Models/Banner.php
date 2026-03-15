<?php

namespace App\Models;

class Banner extends \Model
{
    protected $table = 'site_banners';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'slug',
        'image_url',
        'link_url',
        'alt_text',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
