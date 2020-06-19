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
use DB;
use App\Log_user;
use Excel;
use Carbon\Carbon;

class EstimateController extends Controller
{
    public function get_add()
    {
      $year4=[];
      $year3=[];
      $year2=[];
      $year1=[];
      $now=[];
      $explan=[];
      $all = Master::get();
        foreach ($all as $key => $value) {
          $year4[date("Y",strtotime("-4 year"))+543][$value->account] = 0;
          $year3[date("Y",strtotime("-3 year"))+543][$value->account] = 0;
          $year2[date("Y",strtotime("-2 year"))+543][$value->account] = 0;
          $year1[date("Y",strtotime("-1 year"))+543][$value->account] = 0;
          $now[date("Y")+543][$value->account] = 0;
          $explan[date("Y")+543][$value->account] = 0;
        }
        // dd($all);
      $list = DB::table('estimates')
        ->select('stat_year','account','explanation', DB::raw('SUM(budget) as budget'))
        ->where('stat_year','>=',(date("Y",strtotime("-4 year"))+543))
        ->groupBy('stat_year','account','explanation')->get()->toArray();

        foreach ($list as $key => $value) {
          if($value->stat_year == date("Y",strtotime("-4 year"))+543){
            $year4[$value->stat_year][$value->account] = $value->budget;
          }elseif ($value->stat_year == date("Y",strtotime("-3 year"))+543) {
            $year3[$value->stat_year][$value->account] = $value->budget;
          }elseif ($value->stat_year == date("Y",strtotime("-2 year"))+543) {
            $year2[$value->stat_year][$value->account] = $value->budget;
          }elseif($value->stat_year == date("Y",strtotime("-1 year"))+543){
            $year1[$value->stat_year][$value->account] = $value->budget;
          }else{
            $now[$value->stat_year][$value->account] = $value->budget;
            $explan[$value->stat_year][$value->account] = $value->explanation;
          }
        }

      return view('add_est',['now' => $now,'year1' => $year1,'year2' => $year2,'year3' => $year3,'year4' => $year4 ,'explan' => $explan]);
    }

    public function post_add(Request $request)
    {
      $this->validate($request,[
         'stat_year'=>'required|numeric',
         'name_reqs'=>'required',
         'phone' => 'required|numeric'
      ]);
      foreach ($request->budget as $key => $val) {
        // ใช้วิธี delete insert
        if($val != null){ // caseที่มีทั้งกรอกงบเพิ่มและลบงบประมาณ
          DB::table('estimates')->where('stat_year', date("Y")+543)->where('account',$key)->update(['deleted_at' => \Carbon::now()]);
          $estimate = new Estimate;
          $estimate->stat_year = date("Y")+543;
          $estimate->account = $key;
          $estimate->budget = $val;
          $estimate->center_money = Auth::user()->center_money;
          $estimate->save();
        }else{ // caseที่มีการลบข้อมูลอย่างเดียวไม่มีการกรอกงบเพิ่ม
          DB::table('estimates')->where('stat_year', date("Y")+543)->where('account',$key)->update(['deleted_at' => \Carbon::now()]);
        }
      }
// dd($request->explan);
      foreach ($request->explan as $key => $val) {
        if($val != null){
          if(!is_null($request->budget[$key])){
            DB::table('estimates')
              ->where('account',$key)
              ->where('stat_year',date("Y")+543)
              ->update(['explanation' => $val, 'updated_at' => \Carbon::now()]);
          }
        }
      }

      $data = new User_request;
      $data->stat_year = $request->stat_year;
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = Auth::user()->part;
      $data->name = $request->name_reqs;
      $data->phone = $request->phone;
      $data->center_money = Auth::user()->center_money;
      $data->type = 'งบทำการ';
      $data->save();

      if($data){
        return back()->with('success', 'Insert successfully.');
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
     if($data->count() > 0)
     {
       $num = 1;
      foreach($data->toArray() as $key => $value)
      {
        $i = 0;
       foreach($value as $row)
       {
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
      if(!empty($insert_data))
      {
        for($j = 1; $j <= count($insert_data); $j++ ){
          // dd($insert_data[$j++]['account']);
          $insert = new Master;
          $insert->account = $insert_data[$j++]['account'];
          $insert->name = $insert_data[$j]['name'];
          $insert->save();
        }
      }
     }
     return back()->with('success', 'Excel Data Imported successfully.');
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

      return back()->with('success', 'Insert data successfully.');
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

      return back()->with('success', 'Update data successfully.');
    }

    public function post_delete_master(Request $request)
    {
      $delete = Master::find($request->id);
      $delete->delete();

      return Redirect::to('/estimate/add/master');
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
     if($data->count() > 0)
     {
       $num = 1;
      foreach($data->toArray() as $key => $value)
      {
        $i = 0;
       foreach($value as $row)
       {
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
      if(!empty($insert_data))
      {
        for($j = 1; $j <= count($insert_data); $j++ ){
          if(!empty($insert_data[$j]['stat_year'])){
            $insert = new Estimate;
            $insert->stat_year = $insert_data[$j++]['stat_year'];
            $insert->account = $insert_data[$j++]['account'];
            $insert->budget = $insert_data[$j++]['budget'];
            $insert->center_money = $insert_data[$j]['center_money'];
            $insert->save();
          }else{
            break;
          }
        }
      }
     }
     return back()->with('success', 'Excel Data Imported successfully.');
    }

    public function post_edit_account(Request $request)
    {
      // dd(2321);
      $update = Estimate::find($request->id);
      $update->account = $request->account;
      $update->update();

      return back()->with('success', 'Insert successfully.');
    }

    public function get_view()
    {
      $view = Estimate::where('stat_year',(date('Y')+543))->get()->toArray();

      return view('view_all',['view' => $view,'yy' => (date('Y')+543)]);
    }

    public function post_view(Request $request)
    {
      $view = Estimate::where('stat_year',$request->year)->get()->toArray();
      return view('view_all',['view' => $view,'yy' => $request->year]);
    }

    public function post_approve(Request $request)
    {
      $approve = new Approve_log;
      $approve->user_approve = $request->user_approve;
      $approve->stat_year = $request->year;
      $approve->budget = $request->budget;
      $approve->save();

      $update = DB::table('estimates')
        ->where('stat_year', $request->year)
        ->update(['status' => 1]);

      if($update){
        return back()->with('success', 'อนุมัติแล้ว');
      }
    }
}
