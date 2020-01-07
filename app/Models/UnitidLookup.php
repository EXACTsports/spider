<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitidLookup extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'unitid_lookups';
    protected $guarded =[];
}
