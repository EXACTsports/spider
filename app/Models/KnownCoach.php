<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnownCoach extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'known_coaches';
    protected $guarded = [];
}
