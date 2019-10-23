<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $casts = ['meta' => 'array'];

    protected $attributes = ['meta' => '{}'];
}
