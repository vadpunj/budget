<?php
// Helpers files
use App\Master;
use App\Estimate;
use App\Role;

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

  public static function get_role($value)
  {

    $role = Role::where('id',$value)->first();
    return $role['role_name'];
  }

  public static function get_role_all()
  {
    $role = Role::all();
    // dd($role['0']->role_name);
    return $role;
  }
  public static function get_last_version($date,$center_money)
  {
    $last_ver  = Estimate::where('stat_year',$date)->where('center_money',$center_money)->latest()->first();
    // dd($last_ver);
    if($last_ver == NULL){
      return NULL;
    }else{
      return $last_ver->version;
    }
  }
}

 ?>
