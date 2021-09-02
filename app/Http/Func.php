<?php
// Helpers files
use App\Master;
use App\Estimate;
use App\Role;
use App\Structure;
use App\Shutdown;
use App\Cmmt;

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
    $last_ver  = Estimate::where('stat_year',$date)->where('center_money',$center_money)->orderBy('version','DESC')->first();
    // dd($last_ver);
    if($last_ver == NULL){
      return NULL;
    }else{
      return $last_ver->version;
    }
  }
  public static function insert_verion($date,$center_money)
  {
    $have_ver  = Estimate::where('stat_year',$date)->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('center_money',$center_money)->orderBy('version','DESC')->first();
    // dd($last_ver);
    if($have_ver == NULL){
      $last_ver = Estimate::select('version')->where('stat_year',$date)->where('center_money',$center_money)->orderBy('version','DESC')->first();
      // dd($last_ver);
      return $last_ver+1;
    }else{
      return $have_ver->version;
    }
  }
  public static function get_idcostname($fund,$center_money)
  {
    $id  = Structure::select('CostCenterID')->where('CostCenterTitle','like','%'.$fund.'%')->where('CostCenterName','like','%'.$center_money.'%')->groupBy('CostCenterID')->first();
    // dd($id);
    if($id == NULL){
      return NULL;
    }else{
      return $id;
    }
  }
  public static function get_cost_title($center_money)
  {
    $name = Structure::where('CostCenterID',$center_money)->first();
    if($name == NULL){
      return NULL;
    }else{
      return $name->CostCenterTitle;
    }
  }
  public static function get_name_costcenter($center_money)
  {
    $name = Structure::where('CostCenterID',$center_money)->first();
    if($name == NULL){
      $cost = Structure::where('FundID',$center_money)->whereNull('CostCenterID')->first();
      if($cost == NULL){
        return NULL;
      }else{
        return $cost->CostCenterName;
      }
    }else{
      return $name->CostCenterName;
    }
  }

  public static function get_name_costcenter_by_divID($div_center)
  {
    $name = Structure::where('FundsCenterID',$div_center)->whereNull('CostCenterID')->orWhere('CostCenterName','สาย%')->first();
    if($name == NULL){
      return NULL;
    }else{
      return $name->CostCenterName;
    }
  }
  public static function FundID_name($fund_id)
  {
    $name = Structure::select(DB::raw('DISTINCT FundID,CostCenterName'))->where('FundID',$fund_id)->whereNotNull('FundID')->whereNull('CostCenterID')->get();

    if($name[0] == NULL){
      return NULL;
    }else{
      return $name[0]->CostCenterName;
    }
  }
  public static function FundID_name_all($fund_id)
  {
    $name = Structure::select(DB::raw('DISTINCT FundID,CostCenterName'))->where('FundID',$fund_id)->whereNotNull('CostCenterID')->get();
// dd(count($name));
    if(count($name) == 0){
      return NULL;
    }else{
      return $name;
    }
  }
  public static function rang_shutdown($date)
  {
    $date = Shutdown::where('start_date','<=',$date)
      ->where('end_date','>=',$date)->first();
      // dd(is_null($date));
    return is_null($date);
  }
  public static function list_cmmt($account)
  {
    $cmmt = Master::where('account',$account)->get();
      // dd($cmmt);
    return $cmmt;
  }
  public static function get_name_fundID($fundid)
  {
    // SELECT CostCenterName
    // FROM `structures`
    // where FundID = '2V00100' and CostCenterID is null and CostCenterName like 'ฝ่าย%'
    // group by CostCenterName
    $name = Structure::select('CostCenterName')
    ->where('FundID',$fundid)
    ->whereNull('CostCenterID')
    ->where('CostCenterName','like','ฝ่าย%')
    ->get();
    return $name;

  }
  public static function get_field_name($fund_center)
  {
    $name = Structure::select('CostCenterName')
    ->where('FundsCenterID',$fund_center)
    ->whereNull('CostCenterID')
    ->whereNull('FundID')
    ->get()->first();
    // dd($name->CostCenterName);
    if($name == NULL){
      return NULL;
    }else{
      return $name->CostCenterName;
    }

  }
  public static function get_office_name($fundID)
  {
    $name = Structure::select('CostCenterName')
    ->where('FundID',$fundID)
    ->whereNull('CostCenterID')
    ->get()->first();
    // dd($name->CostCenterName);
    if($name == NULL){
      return NULL;
    }else{
      return $name->CostCenterName;
    }
  }
  public static function get_part_name($center_money)
  {
    $name = Structure::select('CostCenterTitle','CostCenterName')
    ->where('CostCenterID',$center_money)
    ->get()->first();
    // dd($name->CostCenterName);
    if($name == NULL){
      return NULL;
    }else{
      return $name;
    }
  }
  public static function get_center_name($center_money)
  {
    $name = Structure::select('CostCenterName')
    ->where('CostCenterID',$center_money)
    ->get()->first();
    if($name == NULL){
      return NULL;
    }else{
      return $name->CostCenterName;
    }
  }
  public static function get_funID($center_money)
  {
    $name = Structure::select('FundID')
    ->where('CostCenterID',$center_money)
    ->get()->first();
    if($name == NULL){
      return NULL;
    }else{
      return $name->FundID;
    }
  }
}

 ?>
