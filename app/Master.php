<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{
  use SoftDeletes;

  protected $table = 'masters';

  protected $dates = ['deleted_at'];
}
