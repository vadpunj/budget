<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
  use SoftDeletes;

  protected $table = 'budgets';

  protected $dates = ['deleted_at'];
}
