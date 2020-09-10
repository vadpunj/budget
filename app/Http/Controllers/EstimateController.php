<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\User_request;
use App\Master;
use App\Estimate;
use App\Approve_log;
use App\Structure;
use App\Export_estimate;
use DB;
use Func;
use App\Log_user;
use Excel;
use Carbon\Carbon;

class EstimateController extends Controller
{
    public function get_add()
    {
      // dd(2321);
      $year3=[];
      $year2=[];
      $year1=[];
      $now=[];
      $status=[];
       $all = Master::get();
      // $all = DB::table('masters')
      //       ->join('estimates', 'masters.account', '=', 'estimates.account')
      //       ->select('masters.account')
      //       ->where('estimates.stat_year' ,'>=',(date("Y",strtotime("-3 year"))+543))
      //       ->whereNull('masters.deleted_at')
      //       ->whereNull('estimates.deleted_at')
      //       ->get();
        foreach ($all as $key => $value) {
          $year3[date("Y",strtotime("-3 year"))+543][$value->account] = 0;
          $year2[date("Y",strtotime("-2 year"))+543][$value->account] = 0;
          $year1[date("Y",strtotime("-1 year"))+543][$value->account] = 0;
          $now[date("Y")+543][$value->account] = 0;
          $status[date("Y")+543][$value->account] = NULL;
        }
        // dd($year2);
        // $last_ver = Func::get_last_version(date("Y")+543 ,Auth::user()->center_money);
        // dd($last_ver);
        for($i = date("Y",strtotime("-3 year"))+543 ; $i <= date("Y")+543 ; $i++){
          $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          // $last_ver  = Estimate::where('center_money',\Auth::user()->center_money)->latest()->first();
    // dump($last_ver);
          if($last_ver != NULL){
            $list = DB::table('estimates')
              ->select('stat_year','account','status', DB::raw('SUM(budget) as budget'))
              ->where('stat_year',$i)
              ->whereNull('deleted_at')
              ->where('version', $last_ver)
              ->where('center_money',\Auth::user()->center_money)
              ->groupBy('stat_year','account','version','status')->get()->toArray();
              // dd($list);
              foreach ($list as $key => $value) {
                // dd($value[$i]->stat_year);
                if ($value->stat_year == date("Y",strtotime("-3 year"))+543) {
                  $year3[$value->stat_year][$value->account] = $value->budget;
                }if ($value->stat_year == date("Y",strtotime("-2 year"))+543) {
                  $year2[$value->stat_year][$value->account] = $value->budget;
                }if($value->stat_year == date("Y",strtotime("-1 year"))+543){
                  $year1[$value->stat_year][$value->account] = $value->budget;
                }if($value->stat_year == date("Y")+543){
                  $now[$value->stat_year][$value->account] = $value->budget;
                  $status[$value->stat_year][$value->account] = $value->status;
                }
              }
          }else{
            $list = NULL;
          }
        }
// dd($now);

// dd($year1);
      return view('add_est',['status' => $status,'list' => $list, 'now' => $now,'year1' => $year1,'year2' => $year2,'year3' => $year3]);
    }

    public function post_add(Request $request)
    {
      // dd($request->all());
      $this->validate($request,[
         'stat_year'=>'required|numeric',
         // 'name_reqs'=>'required',
         'phone' => 'required|numeric'
      ]);
      // dd($request->budget);
      $last = Func::get_last_version(date('Y')+543,Auth::user()->center_money);

      // $last = Estimate::where('center_money',\Auth::user()->center_money)->latest()->first();
      foreach ($request->budget as $key => $val) {

        if($val != null){
          $insert = new Estimate;
          $insert->version = $last+1;
          $insert->stat_year = $request->stat_year;
          $insert->account = $key;
          $insert->budget = $val;
          $insert->status = NULL;
          $insert->center_money = \Auth::user()->center_money;
          $insert->fund_center = substr_replace(\Auth::user()->center_money,"00",5);
          $insert->created_by = \Auth::user()->emp_id;
          $insert->save();
        }

      }

      $data = new User_request;
      $data->stat_year = $request->stat_year;
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = Auth::user()->part;
      $data->name =  Auth::user()->emp_id;
      $data->phone = $request->phone;
      $data->center_money = Auth::user()->center_money;
      $data->type = 'งบทำการ';
      $data->save();

      if($data){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function get_importfile()
    {
      return view('import_master');
    }

    public function post_importfile(Request $request)
    {
      config(['excel.import.heading' => 'original' ]);
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xlsx'
      ]);

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();
     // Storage::disk('log')->put($name, File::get($request->file('select_file')));
     // // $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     // $data = Excel::load($path)->get();

     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $path.$name;
     $insert_log->type_log = 'ไฟล์master';
     $insert_log->save();

     $key_name = ['account','name'];
// dd($data->toArray());
    foreach($data->toArray() as $value){
      // dd($value['รายการภาระผูกพัน']);
      if($value['รายการภาระผูกพัน']){
        $insert = new Master;
        $insert->account = $value['รายการภาระผูกพัน'];
        $insert->name = $value['ชื่อ'];
        $insert->save();
      }

    }
     // if($data->count() > 0){
     //   $num = 1;
     //  foreach($data->toArray() as $key => $value){
     //    $i = 0;
     //   foreach($value as $row){
     //     if(!is_null($row)){
     //       $insert_data[$num][$key_name[$i]] = $row;
     //       $num++;
     //       $i++;
     //     }else{
     //       break;
     //     }
     //   }
     //  }
     //  // dd($insert_data);
     //  if(!empty($insert_data)){
     //    for($j = 1; $j <= count($insert_data); $j++ ){
     //      // dd($insert_data[$j++]['account']);
     //      $insert = new Master;
     //      $insert->account = $insert_data[$j++]['account'];
     //      $insert->name = $insert_data[$j]['name'];
     //      $insert->save();
     //    }
     //  }
     // }
     if($insert){
       return back()->with('success', 'บันทึกข้อมูลแล้ว');
     }
     // return back()->with('success', 'Excel Data Imported successfully.');
    }

    public function get_master()
    {
      $data = Master::get();
      return view('master',['data' => $data]);
    }

    public function post_master(Request $request)
    {
      $this->validate($request, [
        'account' => 'required|min:8',
        'name'  => 'required'
      ]);

      $insert = new Master;
      $insert->account = $request->account;
      $insert->name = $request->name;
      $insert->save();

      if($insert){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function post_edit_master(Request $request)
    {
      // dd($request->id);
      $this->validate($request, [
        'account' => 'required|min:8',
        'name'  => 'required'
      ]);
      $update = Master::find($request->id);
      $update->account = $request->account;
      $update->name = $request->name;
      $update->update();

      if($update){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function post_delete_master(Request $request)
    {
      $delete = Master::find($request->id);
      $delete->delete();

      if($delete){
        return back()->with('success', 'ลบข้อมูลแล้ว');
      }
      // return Redirect::to('/estimate/add/master');
    }

    public function get_estimate()
    {
      // SELECT * FROM `estimates` where account not in (SELECT account from masters );

      $test =  Estimate::whereNotIn('account', function($query){
          $query->select('account')
          ->from(with(new Master)->getTable());
      })->get()->toArray();

      return view('import_estimate',['data' => $test]);
    }

    public function post_estimate(Request $request)
    {
      config(['excel.import.heading' => 'original' ]);
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xlsx'
      ]);

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();
     // Storage::disk('log')->put($name, File::get($request->file('select_file')));
     // // dd(File::get($request->file('select_file')));
     // $data = Excel::load($path)->get();
     // dd(2432);
     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $path.$name;
     $insert_log->type_log = 'งบทำการ';
     $insert_log->save();

     // $key_name = ['stat_year','account','budget','center_money','fund_center'];
// dd($data->count());
    foreach($data->toArray() as $value){
      if($value['ปีงบ']){
       $insert = new Estimate;
       $insert->stat_year = $value['ปีงบ'];
       $insert->version = 1;
       $insert->account = $value['บัญชี'];
       $insert->budget = $value['เงิน'];
       $insert->center_money = $value['ศูนย์ต้นทุน'];
       $insert->fund_center = $value['ศูนย์เงินทุน'];
       $insert->created_by = Auth::user()->emp_id;
       $insert->save();
      }
    }

     if($insert){
       return back()->with('success', 'Excel Data Imported successfully.');
     }
     // return back()->with('success', 'Excel Data Imported successfully.');
    }

    public function post_edit_account(Request $request)
    {
      // dd(2321);
      $update = Estimate::find($request->id);
      $update->account = $request->account;
      $update->update();

      if($update){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function get_view()
    {
      if(Auth::user()->type == 1 || Auth::user()->type == 4 || Auth::user()->type == 5){
        $center_money = Estimate::select('center_money')->groupBy('center_money')->get();
      }else{
        $center_money = Estimate::select('center_money')->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      }
      // dd($center_money);
      if($center_money->count()){
        // dd($center_money);
        $center = $center_money->first()->center_money;
        $last_ver = Func::get_last_version(date('Y')+543,$center_money->first()->center_money);
        $view = Estimate::select(DB::raw('status,version, center_money,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',(date('Y')+543))
          ->where('version',$last_ver)
          ->where('center_money',$center)
          ->groupBy('status', 'center_money','stat_year','version','account','approve_by1','approve_by2')
          ->get()->toArray();
      }else{
        $last_ver = NULL;
        $view = NULL;
        $center_money = NULL;
        $center = NULL;
      }
      // dd(4444);
      // $last_ver  = Estimate::where('center_money',$center_money->first()->center_money)->latest()->first();

      // dd($center_money->first()->center_money);

      // dd($view[0]["version"]);


      return view('view_all',['views' => $view,'yy' => (date('Y')+543) , 'center_money' => $center_money, 'center'=> $center]);
    }

    public function post_view(Request $request)
    {
      $center_money = Estimate::select('center_money')->groupBy('center_money')->get();
      $last_ver = Func::get_last_version($request->year,$request->center_money);
      // $last_ver  = Estimate::where('center_money',$request->center_money)->latest()->first();
      $view = Estimate::select(DB::raw('status, center_money,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
        ->where('stat_year',$request->year)
        ->where('version',$last_ver)
        ->where('center_money',$request->center_money)
        ->groupBy('status', 'center_money','stat_year','account','approve_by1','approve_by2')
        ->get()->toArray();
// dd($request->center_money);
      // $view = Estimate::where('stat_year',(date('Y')+543))->where('version',$last_ver->version)->get()->toArray();


      return view('view_all',['views' => $view,'yy' => $request->year, 'center_money' => $center_money,'center' => $request->center_money]);
    }

    public function post_approve(Request $request)
    {
      // dd($request->all());
      $last_ver = Func::get_last_version($request->year,$request->center_money);
      // $last_ver  = Estimate::where('center_money',$request->center_money)->latest()->first();
      // dd($last_ver->version);
      if(Auth::user()->type == 4 || Auth::user()->type == 1){
        if($request->btn == "true"){
          // dd(4444);
          $update = DB::table('estimates')
            ->where('stat_year', $request->year)
            ->whereIn('account', $request->approve1)
            ->where('version',$last_ver)
            ->where('center_money',$request->center_money)
            ->update(['status' => 0,'approve_by1' => Auth::user()->emp_id]);

          $approve = new Approve_log;
          $approve->user_approve = Auth::user()->emp_id;
          $approve->stat_year = $request->year;
          $approve->version = $last_ver;
          $approve->center_money = $request->center_money;
          $approve->save();
          if($update){
            return back()->with('success', 'อนุมัติแล้ว');
          }
        }elseif($request->btn == "false"){
          // dd($request->approve1);
          $update = DB::table('estimates')
            ->where('stat_year', $request->year)
            ->whereIn('account', $request->approve1)
            ->where('version',$last_ver)
            ->where('center_money',$request->center_money)
            ->update(['status' => 4,'approve_by1' => NULL]);
            if($update){
              return back()->with('success', 'ยกเลิกการอนุมัติแล้ว');
            }
        }
      }elseif(Auth::user()->type == 5){

        if($request->btn == "true"){
          $update = DB::table('estimates')
            ->where('stat_year', $request->year)
            ->whereIn('account', $request->approve2)
            ->where('version',$last_ver)
            ->where('center_money',$request->center_money)
            ->update(['status' => 1,'approve_by2' => Auth::user()->emp_id]);

          $approve = new Approve_log;
          $approve->user_approve = Auth::user()->emp_id;
          $approve->stat_year = $request->year;
          $approve->version = $last_ver;
          $approve->center_money = $request->center_money;
          $approve->save();
// dd($request->approve2[0]);
          for($i=0 ;$i < count($request->approve2) ; $i++){
            $export = new Export_estimate;
            $export->version = 1;
            $export->year = $request->year;
            $export->fund_center = substr_replace($request->center_money,"00",5);
            $export->center_money = $request->center_money;
            $export->account = $request->approve2[$i];
            $export->budget = $request->budget[$i];
            $export->user_id = Auth::user()->emp_id;
            $export->save();
          }
          if($update){
            return back()->with('success', 'อนุมัติแล้ว');
          }
        }elseif($request->btn == "false"){
          $update = DB::table('estimates')
            ->where('stat_year', $request->year)
            ->whereIn('account', $request->approve2)
            ->where('version',$last_ver)
            ->where('center_money',$request->center_money)
            ->update(['status' => 3,'approve_by2' => NULL]);

              Export_estimate::where('year',$request->year)
              ->whereIn('account', $request->approve2)
              ->where('center_money',$request->center_money)
              ->delete();

            if($update){
              return back()->with('success', 'ยกเลิกการอนุมัติแล้ว');
            }
        }
      }
    }
    public function get_struc()
    {
      $data = Structure::get();
      return view('import_struc',['data' => $data]);
    }
    public function post_struc(Request $request)
    {
      // dd(3333);
      config(['excel.import.heading' => 'original' ]);
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xlsx'
      ]);

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();
     // dd($data);
     // Storage::disk('log')->put($name, File::get($request->file('select_file')));
     // // $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     // $data = Excel::load($path)->get();

     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $path.$name;
     $insert_log->type_log = 'ไฟลstructure';
     $insert_log->save();

     // $key_name = ['Company','Division','FundsCenterID','CostCenterID','CostCenterTitle','CostCenterName'];
// dd($data->toArray());
    foreach($data->toArray() as $value){
      if($value['Company']){
        $insert = new Structure;
        $insert->Company = $value['Company'];
        $insert->Division = $value['Division'];
        $insert->FundsCenterID = $value['FundsCenterID'];
        $insert->CostCenterID = $value['CostCenterID'];
        $insert->CostCenterTitle = $value['CostCenterTitle'];
        $insert->CostCenterName = $value['CostCenterName'];
        $insert->save();
      }

    }

     if($insert){
       return back()->with('success', 'Excel Data Imported successfully.');
     }
    }

    public function edit_struc(Request $request)
    {
      $edit = Structure::find($request->id);
      $edit->Company = $request->Company;
      $edit->Division = $request->Division;
      $edit->FundsCenterID = $request->FundsCenterID;
      $edit->CostCenterID = $request->CostCenterID;
      $edit->CostCenterTitle = $request->CostCenterTitle;
      $edit->CostCenterName = $request->CostCenterName;
      $edit->update();

      if($edit){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function delete_struc(Request $request)
    {
      $delete = Structure::find($request->id);
      $delete->delete();

      if($delete){
        return back()->with('success', 'ลบข้อมูลแล้ว');
      }
    }

    public function post_add_struc(Request $request)
    {
      $add = new Structure;
      $add->Company = $request->Company;
      $add->Division = $request->Division;
      $add->FundsCenterID = $request->FundsCenterID;
      $add->CostCenterID = $request->CostCenterID;
      $add->CostCenterTitle = $request->CostCenterTitle;
      $add->CostCenterName = $request->CostCenterName;
      $add->save();

      if($add){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }
    public function get_export()
    {
      return view('export_sap');
    }

    public function export_sap(Request $request)
    {
      // $data_array[] = 0;
      $sap = Export_estimate::select('year','version','fund_center','account',DB::raw('SUM(budget) as budget'))
        ->where('year', $request->stat_year)
        ->groupBy('year','version','fund_center','account')
        ->get()->toArray();
        // dd($sap);
        foreach ($sap as $key => $value) {
              $data[] = array(
                'year' => $value['year']-543,
                'from' => 1,
                'to' => 1,
                'version' => $value['version'],
                'fund' => 'oper',
                'func.a' => 'z0',
                'fund_center' => $value['fund_center'],
                'cmmt' => $value['account'],
                'amount' => $value['budget']
              );
        }
        if(!isset($data)){
          dd('ไม่มีข้อมูล');
        }
        // dd($data);
      Excel::create('Estimate'.$request->stat_year,function($excel) use ($data){
        $excel->setTitle('Estimate');
        $excel->sheet('Estimate',function($sheet) use ($data){
          $sheet->fromArray($data,null,'A1',false,false);
        });
      })->download('xlsx');
      // dd($data);
    }

    public function get_status()
    {
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->groupBy('center_money')->orderBy('center_money','ASC')->get();
      }else{
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      }
      // dd($center);
      if($center->count()){
        // dd(9999);
        $first = $center->first();
        // dd($first->center_money);
        $firstcen = $first->center_money;
        $last_ver = Func::get_last_version(date('Y')+543,$firstcen);
  // dd($last_ver);
        $get_status = Estimate::select('stat_year','center_money','status',DB::raw('SUM(budget) as budget'))
        ->where('center_money',$firstcen)
        ->where('stat_year',date('Y')+543)
        ->where('version',$last_ver)
        ->groupBy('status','center_money','stat_year')
        ->get()->toArray();
  // dd($get_status);
      }else{
        $firstcen = NULL;
        $center = NULL;
        $get_status = NULL;
      }


// dd($status);
      return view('status_report',['status' => $get_status,'year' => date('Y')+543,'center' => $center,'first'=> $firstcen]);
    }

    public function post_status(Request $request)
    {
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->groupBy('center_money')->orderBy('center_money','ASC')->get();
      }else{
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',$request->center_money)->groupBy('center_money')->get();
      }

      $first = $request->center_money;
      $last_ver = Func::get_last_version(date('Y')+543,$first);
// dd($last_ver);
      $get_status = Estimate::select('stat_year','center_money','status',DB::raw('SUM(budget) as budget'))
      ->where('center_money',$first)
      ->where('stat_year',date('Y')+543)
      ->where('version',$last_ver)
      ->groupBy('status','center_money','stat_year')
      ->get()->toArray();
        // dd($get_status);

      return view('status_report',['status' => $get_status,'year' => date('Y')+543,'center' => $center,'first' => $first]);
    }

    public function get_version()
    {
      $last_ver = Func::get_last_version(date('Y')+543,Auth::user()->center_money);

      // $last_ver  = Estimate::where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->latest()->first();
      // dd($last_ver->version);
      $data = Estimate::where('version',$last_ver)
      ->where('stat_year', date('Y')+543)
      ->where('center_money',Auth::user()->center_money)
      ->get();

      // dd($last_ver->version);
      return view('view_version', ['data' => $data,'versions' => $last_ver,'version' => $last_ver]);
    }
    public function post_version(Request $request)
    {
      $last_ver = Func::get_last_version(date('Y')+543,Auth::user()->center_money);

      // $last_ver  = Estimate::where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->latest()->first();
      $data = Estimate::where('version',$request->version)
        ->where('stat_year', date('Y')+543)
        ->where('center_money',Auth::user()->center_money)
        ->get();

      return view('view_version', ['data' => $data,'versions' => $last_ver ,'version' => $request->version]);
    }

    public function get_view_estimate()
    {
      // dd(998);
      return view('view_estimate');
    }
    public function post_view_estimate(Request $request)
    {
      if(Auth::user()->type == "5"){
        $this->validate($request, [
          'account'  => 'required',
          'center_money' => 'required'
        ]);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money','%'.$request->center_money.'%')
          ->where('stat_year','>=',date('Y')+542)
          ->groupBy('account','stat_year')->get();
      }else{
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year','>=',date('Y')+542)
          ->groupBy('account','stat_year')->get();
      }
      $data =[];
      foreach($view as $value){
        $data[$value->account][$value->stat_year] = $value->budget;
      }
      // dd($data);
      return view('view_estimate',['data' => $data]);
    }

    public function print_all(Request $request)
    {
      // $center_money = Estimate::select('center_money')->groupBy('center_money')->get();
      $last_ver = Func::get_last_version($request->year,$request->center_money);
      // $last_ver  = Estimate::where('center_money',$request->center_money)->latest()->first();
      $view = Estimate::select(DB::raw('status, center_money,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
        ->where('stat_year',$request->year)
        ->where('version',$last_ver)
        ->where('center_money',$request->center_money)
        ->groupBy('status', 'center_money','stat_year','account','approve_by1','approve_by2')
        ->get()->toArray();
      foreach ($view as $key => $value) {
        if($value['status'] == "0"){
          $status = 'ฝ่าย/เขต อนุมัติแล้ว';
        }elseif($value['status'] == "1"){
          $status = 'งบประมาณอนุมัติแล้ว';
        }elseif($value['status'] == NULL){
          $status = 'งบประมาณรอพิจารณา';
        }elseif($value['status'] == "4"){
          $status = 'แก้ไขงบประมาณ';
        }elseif($value['status'] == "3"){
          $status = 'วง.ขอแก้ไขงบ';
        }
        $data[] = array(
          'year' => $value['stat_year'],
          'center' => $value['center_money'],
          'account' => $value['account'],
          'amount' => $value['budget'],
          'status' => $status
        );
      }
      if(!isset($data)){
        dd('ไม่มีข้อมูล');
      }
      // dd($data);
      Excel::create('View Estimate',function($excel) use ($data){
        $excel->setTitle('Estimate');
        $excel->sheet('Estimate',function($sheet) use ($data){
          $sheet->fromArray($data,null,'A1',false,false);
        });
      })->download('xlsx');
    }
}
