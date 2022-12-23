<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stat_year extends Model
{
  use SoftDeletes;
  protected $table = 'stat_years';
  protected $dates = ['deleted_at'];
}
