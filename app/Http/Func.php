<?php
// Helpers files
use App\Master;
use App\Estimate;

class Func{
  public static function get_date($value)
  {
    $m = date('m',strtotime($value));
    $month = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    $year = date('Y',strtotime($value));
    $day = date('d',strtotime($value));


    return $day.' '.$month[$m].' '.$year;

  }
  public static function get_account($value)
  {

    $list_name  = Master::where('account',$value)->first();
// dd($list_name->name);
    return $list_name['name'];

  }
}

 ?>
