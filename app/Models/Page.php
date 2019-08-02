<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $casts = ['meta' => 'array'];

    protected $attributes = ['meta' => '{}'];
}
