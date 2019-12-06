<?php
// Helpers files


class Func{
  public static function get_date($value)
  {
    $m = date('m',strtotime($value));
    $month = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    $year = date('Y',strtotime($value));
    $day = date('d',strtotime($value));


    return $day.' '.$month[$m].' '.$year;

  }
  
}

 ?>
