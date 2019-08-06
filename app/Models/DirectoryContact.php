<?php

namespace App\Models;

use Jenssegers\Model\Model;

class DirectoryContact extends Model
{
    protected $attributes = [
        'name' => '',
        'email' => '',
        'phone' => ''
    ];
}
