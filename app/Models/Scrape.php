<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scrape extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'scrapes';
    protected $guarded = [];
}
