<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $casts = ['meta' => 'array'];

    protected $attributes = ['meta' => '{}'];
}
