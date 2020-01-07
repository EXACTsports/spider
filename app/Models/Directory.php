<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $connection = 'v1';
    protected $table = 'colleges_directories';

    protected $casts = ['meta' => 'array'];

    protected $attributes = ['meta' => '{}'];

    public function scopeUnscraped(Builder $query)
    {
        return $query->whereNotIn('id', Scrape::where('created_at', '>', Carbon::parse('-1 month'))->pluck('id')->toArray());
    }
}
