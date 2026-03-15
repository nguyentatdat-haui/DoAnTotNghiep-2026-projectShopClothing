<?php

namespace App\Repositories;

use App\Models\Color;

class ColorRepository extends BaseRepository
{
    protected $table = 'colors';
    protected $model = Color::class;


   
}
