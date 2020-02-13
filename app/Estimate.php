<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
  use SoftDeletes;

  protected $table = 'estimates';

  protected $dates = ['deleted_at'];
}
