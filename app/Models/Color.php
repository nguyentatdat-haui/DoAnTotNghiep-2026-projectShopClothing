<?php

namespace App\Models;

class Color extends \Model
{
    protected $table = 'colors';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    public function __construct($attributes = [])
    {
        parent::__construct();
        $this->setAttributes($attributes);
    }
}
