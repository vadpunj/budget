<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export_estimate extends Model
{
    use SoftDeletes;
    protected $table = 'export_estimates';
    protected $dates = ['deleted_at'];
}
