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
        for($i = date("Y",strtotime("-3 year"))+543 ; $i <= date("Y")+543 ; $i++){
          $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          // $last_ver  = Estimate::where('center_money',\Auth::user()->center_money)->latest()->first();
    // dump($last_ver);
          if($last_ver != NULL){
            $list = DB::table('estimates')
              ->select('stat_year','account','status', DB::raw('SUM(budget) as budget'))
              ->where('stat_year','>=',$i)
              ->whereNull('deleted_at')
              ->where('version', $last_ver)
              ->where('center_money',\Auth::user()->center_money)
              ->groupBy('stat_year','account','version','status')->get()->toArray();
              $arr[] = $list;
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
          }
        }
// dd($now);
//   $j = 0;
//         foreach ($arr as $key => $value) {
//           // dd($value[$i]->stat_year);
//           if ($value[$j]->stat_year == date("Y",strtotime("-3 year"))+543) {
//             $year3[$value[$j]->stat_year][$value[$j]->account] = $value[$j]->budget;
//           }if ($value[$j]->stat_year == date("Y",strtotime("-2 year"))+543) {
//             $year2[$value[$j]->stat_year][$value[$j]->account] = $value[$j]->budget;
//           }if($value[$j]->stat_year == date("Y",strtotime("-1 year"))+543){
//             $year1[$value[$j]->stat_year][$value[$j]->account] = $value[$j]->budget;
//           }if($value[$j]->stat_year == date("Y")+543){
//             $now[$value[$j]->stat_year][$value[$j]->account] = $value[$j]->budget;
//             $status[$value[$j]->stat_year][$value[$j]->account] = $value[$j]->status;
//           }
//           $j++;
//         }
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
     if($data->count() > 0){
       $num = 1;
      foreach($data->toArray() as $key => $value){
        $i = 0;
       foreach($value as $row){
         if(!is_null($row)){
           $insert_data[$num][$key_name[$i]] = $row;
           $num++;
           $i++;
         }else{
           break;
         }
       }
      }
      // dd($insert_data);
      if(!empty($insert_data)){
        for($j = 1; $j <= count($insert_data); $j++ ){
          // dd($insert_data[$j++]['account']);
          $insert = new Master;
          $insert->account = $insert_data[$j++]['account'];
          $insert->name = $insert_data[$j]['name'];
          $insert->save();
        }
      }
     }
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

     $key_name = ['stat_year','account','budget','center_money'];
// dd($data->count());
     if($data->count() > 0){
       $num = 1;
      foreach($data->toArray() as $key => $value){
        $i = 0;
       foreach($value as $row){
         // if(!is_null($row)){
           $insert_data[$num][$key_name[$i]] = $row;
           $num++;
           $i++;
         // }else{
         //   break;
         // }
       }
      }
      // dd($insert_data);
      if(!empty($insert_data)){
        for($j = 1; $j <= count($insert_data); $j++ ){
          if(!empty($insert_data[$j]['stat_year'])){
            $insert = new Estimate;
            $insert->stat_year = $insert_data[$j++]['stat_year'];
            $insert->version = 1;
            $insert->account = $insert_data[$j++]['account'];
            $insert->budget = $insert_data[$j++]['budget'];
            $insert->center_money = $insert_data[$j]['center_money'];
            $insert->created_by = Auth::user()->emp_id;
            $insert->save();
          }else{
            break;
          }
        }
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
      $last_ver = Func::get_last_version(date('Y')+543,$center_money->first()->center_money);
      // $last_ver  = Estimate::where('center_money',$center_money->first()->center_money)->latest()->first();

      // dd($center_money->first()->center_money);
      $view = Estimate::select(DB::raw('status,version, center_money,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
        ->where('stat_year',(date('Y')+543))
        ->where('version',$last_ver)
        ->where('center_money',$center_money->first()->center_money)
        ->groupBy('status', 'center_money','stat_year','version','account','approve_by1','approve_by2')
        ->get()->toArray();
      // dd($view[0]["version"]);


      return view('view_all',['views' => $view,'yy' => (date('Y')+543) , 'center_money' => $center_money, 'center'=> $center_money->first()->center_money]);
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
          if($update){
            return back()->with('success', 'อนุมัติแล้ว');
          }
        }elseif($request->btn == "false"){
          $update = DB::table('estimates')
            ->where('stat_year', $request->year)
            ->whereIn('account', $request->approve2)
            ->where('version',$last_ver)
            ->where('center_money',$request->center_money)
            ->update(['status' => 0,'approve_by2' => NULL]);
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

     $key_name = ['Company','Division','FundsCenterID','CostCenterID','CostCenterTitle','CostCenterName'];
// dd($data->toArray());
     if($data->count() > 0){
       $num = 1;
      foreach($data->toArray() as $key => $value){
        $i = 0;
       foreach($value as $row){
         if(!is_null($row)){
           $add_data[$num][$key_name[$i]] = $row;
           $num++;
           $i++;
         }else{
           break;
         }
       }
      }
      // dd($insert_data);
      if(!empty($add_data)){
        for($j = 1; $j <= count($add_data); $j++ ){
            $insert = new Structure;
            $insert->Company = $add_data[$j++]['Company'];
            $insert->Division = $add_data[$j++]['Division'];
            $insert->FundsCenterID = $add_data[$j++]['FundsCenterID'];
            $insert->CostCenterID = $add_data[$j++]['CostCenterID'];
            $insert->CostCenterTitle = $add_data[$j++]['CostCenterTitle'];
            $insert->CostCenterName = $add_data[$j]['CostCenterName'];
            $insert->save();
        }
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
      for($i = $request->center_money1 ; $i <= $request->center_money2 ; $i++){
        $last_ver = Estimate::where('center_money',$i)
          ->where('status',1)
          ->where('stat_year',date('Y')+543)
          ->latest()
          ->first();

          if(!is_null($last_ver)){
            $version_array[$i] = $last_ver->version;
          }
      }
      $data=array();
      foreach ($version_array as $key => $value) {
        $read = Estimate::where('center_money',$key)
        ->where('status',1)
        ->where('version',$value)
        ->where('stat_year',$request->stat_year)
        ->get()->toArray();
        // dd($read);
        // $ss = $read;
        // array_push($read,$data);
          $data_array[] = $read;
      }
      // dd($data_array);
      foreach ($data_array as $key => $value) {
        foreach ($value as $key1) {
            $data[] = array(
              'year' => $key1['stat_year']-543,
              '1' => 1,
              '2' => 1,
              '3' => 1,
              '4' => 'oper',
              '5' => 'z0',
              '6' => $key1['center_money'],
              '7' => $key1['account'],
              '8' => $key1['budget']
            );
        }
      }
      Excel::create('Estimate'.$request->center_money1.'-'.$request->center_money2,function($excel) use ($data){
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
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->groupBy('center_money')->get();
      }else{
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      }
      // dd($center_money);
      foreach ($center as $key => $value) {
        // dd($value->center_money);
        $last_ver = Func::get_last_version(date('Y')+543,$value->center_money);

        // $last_ver = Estimate::where('center_money',$value->center_money)
        //   ->where('stat_year',date('Y')+543)
        //   ->latest()
        //   ->first();

          if(!is_null($last_ver)){
            $version_array[$value->center_money] = $last_ver;
          }
      }
      // dd($version_array);
      foreach ($version_array as $key => $version) {
        $get_status = Estimate::select('center_money','status',DB::raw('SUM(budget) as budget'))
        ->where('center_money',$key)
        ->where('stat_year',date('Y')+543)
        ->where('version',$version)
        ->groupBy('status','center_money')
        ->get()->toArray();

        $status_arr[] = $get_status;
      }
      // dd($status_arr);
      foreach ($status_arr as $key => $cent) {
        foreach ($cent as $value) {
          $status[] = array(
            'year' => date('Y')+543,
            'center_money' => $value["center_money"],
            'status' => $value["status"],
            'budget' => $value["budget"]
           );
        }
      }

      return view('status_report',['status' => $status]);
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
}
