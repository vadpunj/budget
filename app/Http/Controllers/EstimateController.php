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
      $all_year3=[];
      $all_year2=[];
      $all_year1=[];
      $all_now=[];
      $status=[];
      $reason=[];

      // $check_button = Estimate::select('status')
      //   ->where('stat_year',date('Y')+543)
      //   ->where('version',Func::get_last_version(date('Y')+543 ,Auth::user()->center_money))
      //   ->where('center_money',Auth::user()->center_money)
      //   ->groupBy('status')->get();
// dd($check_button);
      // if(count($check_button) == 1){
      //   $btn = '';
      //   if($check_button[0]->status == 0 || $check_button[0]->status == 1 || $check_button[0]->status == 3){
      //     $btn = 'disabled';
      //   }
      // }else{
      //   $btn = 'disabled';
      // }
      // query หาชื่อค่าใช้จ่ายทั้งหมด
      $test = DB::table('masters')
        ->whereNull('deleted_at')
         ->whereNotIn('account', function($query)
         {
             $query->select('account')
                   ->from('estimates')
                   ->where('stat_year',date('Y')+543)
                   ->whereNull('deleted_at')
                   ->where('version', Func::get_last_version(date('Y')+543 ,Auth::user()->center_money))
                   ->where('center_money',Auth::user()->center_money)
                   ->groupBy('account');
         })->get();

       foreach ($test as $key => $value) {
         $all_year3[date("Y",strtotime("-3 year"))+543][$value->account] = 0;
         $all_year2[date("Y",strtotime("-2 year"))+543][$value->account] = 0;
         $all_year1[date("Y",strtotime("-1 year"))+543][$value->account] = 0;
         $all_now[date("Y")+543][$value->account] = 0;

       }
       // query ค่าใช้จ่ายงบประมาณที่อนุมัติแล้วในปีก่อนย้อนหลังไป3ปี
        for($i = date("Y",strtotime("-3 year"))+543 ; $i <= date("Y")+542 ; $i++){
          $last_ver = Func::get_last_version($i ,Auth::user()->center_money);

          if($last_ver != NULL){
            $list = DB::table('estimates')
              ->select('stat_year','reason','account','status', DB::raw('SUM(budget) as budget'))
              ->where('stat_year',$i)
              ->whereNull('deleted_at')
              ->where('version', $last_ver)
              ->where('center_money',Auth::user()->center_money)
              ->where('status',1)
              ->groupBy('stat_year','reason','account','version','status')
              ->orderBy('status','DESC')
              ->get()->toArray();
              // dd($list);
              foreach ($list as $key => $value) {
                if ($value->stat_year == date("Y",strtotime("-3 year"))+543) {
                  $year3[$value->stat_year][$value->account] = $value->budget;
                }if ($value->stat_year == date("Y",strtotime("-2 year"))+543) {
                  $year2[$value->stat_year][$value->account] = $value->budget;
                }if($value->stat_year == date("Y",strtotime("-1 year"))+543){
                  $year1[$value->stat_year][$value->account] = $value->budget;
                }
              }

          }else{
            $list = NULL;
          }
        }
        // query ข้อมูลงบปีปัจจุบัน
        $last_ver_now = Func::get_last_version(date("Y")+543,Auth::user()->center_money);
        $list_now = DB::table('estimates')
          ->select('stat_year','reason','account','status', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',date("Y")+543)
          ->whereNull('deleted_at')
          ->where('version', $last_ver_now)
          ->where('center_money',Auth::user()->center_money)
          ->groupBy('stat_year','reason','account','version','status')
          ->orderBy('status','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
            $status[$value->stat_year][$value->account] = $value->status;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
          // dd($list_now);

// dd($reason);
      return view('add_est',['test' => $test,'status' => $status,'list' => $list,'year3' => $year3,'year2' => $year2,'year1' => $year1,'now' => $now,'status' => $status,'reason' => $reason]);
    }

    public function post_add(Request $request)
    {
      // dd($request->reason['5X101103']);

      $this->validate($request,[
         'stat_year'=>'required|numeric',
         // 'name_reqs'=>'required',
         'phone' => 'required|numeric'
      ]);
      // dd($request->budget);
      // บันทึกข้อมูลมูลงบประมาณ
      // เก็บข้อมูลUserที่ของบในtable User_request
      $last = Func::get_last_version(date('Y')+543,Auth::user()->center_money);
// dd(Func::get_cost_title(Auth::user()->center_money));
      // $last = Estimate::where('center_money',\Auth::user()->center_money)->latest()->first();
      foreach ($request->budget as $key => $val) {

        if($val != null){
          $insert = new Estimate;
          $insert->version = $last+1;
          $insert->stat_year = $request->stat_year;
          $insert->account = $key;
          $insert->budget = $val;
          $insert->status = 5;
          $insert->center_money = Auth::user()->center_money;
          $insert->fund_center = Auth::user()->fund_center;
          $insert->cost_title = Func::get_cost_title(Auth::user()->center_money);
          $insert->cost_name = Func::get_name_costcenter(Auth::user()->center_money);
          $insert->reason = $request->reason[$key];
          $insert->created_by = Auth::user()->emp_id;
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
      // import file master
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
     $insert_log->path = $pathreal.$name;
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
      // dd('234');
      // บันทึกข้อมูล master
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
      // แสดงข้อมูลงบที่ยังไม่ได้mapกับบัญชีค่าใช้จ่าย
      $test =  Estimate::whereNotIn('account', function($query){
          $query->select('account')
          ->from(with(new Master)->getTable());
      })->get()->toArray();

      return view('import_estimate',['data' => $test]);
    }

    public function post_estimate(Request $request)
    {
      // import file งบประมาณ
      config(['excel.import.heading' => 'original' ]);
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xlsx'
      ]);

     $last = Func::get_last_version(date('Y')+543 ,Auth::user()->center_money);
     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();
     // dd($data->toArray());
     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $pathreal.$name;
     $insert_log->type_log = 'งบทำการ';
     $insert_log->save();

    foreach($data->toArray() as $value){
      if($value['ปีงบ']){
       $insert = new Estimate;
       $insert->stat_year = $value['ปีงบ'];
       $insert->version = $last+1;
       $insert->account = $value['บัญชี'];
       $insert->budget = $value['เงิน'];
       $insert->center_money = $value['ศูนย์ต้นทุน'];
       $insert->fund_center = $value['ศูนย์เงินทุน'];
       $insert->cost_title = Func::get_cost_title($value['ศูนย์ต้นทุน']);
       $insert->cost_name = Func::get_name_costcenter($value['ศูนย์ต้นทุน']);
       $insert->status = 5;
       $insert->created_by = Auth::user()->emp_id;
       $insert->save();
      }
    }
       if($insert){
         return back()->with('success', 'บันทึกข้อมูลแล้ว');
       }
    }

    public function post_edit_account(Request $request)
    {
      // dd(2321);
      // แก้ไขงบบัญชีงบที่แมพไม่เจอ
      $update = Estimate::find($request->id);
      $update->account = $request->account;
      $update->update();

      if($update){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }

    public function get_view()
    {
      // แสดงเมนูงบเพื่อนให้วง Approve
      if(Auth::user()->type == 1 || Auth::user()->type == 5 || Auth::user()->type == 4){
        $center_money = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('fund_center',Auth::user()->fund_center)->groupBy('center_money')->get();

      }else{
        $center_money = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      }
      // dd($center_money);
      if($center_money->count()){
        foreach($center_money as $data){
          $last_ver = Func::get_last_version(date('Y')+543,$data->center_money);
          $view[] = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
            ->where('stat_year',(date('Y')+543))
            ->where('version',$last_ver)
            ->where('center_money',$data->center_money)
            ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
            ->get()->toArray();
        }

      }else{
        $last_ver = NULL;
        $view = NULL;
      }
// dd($view);
      return view('view_all',['views' => $view]);
    }

    public function post_view(Request $request)
    {

      // dd($request->all());
      // เฉพาะ วง ที่สามารถค้นหาตามชื่อฝ่ายได้
      $view = null;
      $this->validate($request, [
        'cost_title'  => 'required'
      ]);
      $center_money = Estimate::select('center_money')
        ->where('cost_title','like','%'.$request->cost_title.'%')
        ->groupBy('center_money')->get()->toArray();
      // dd($center_money[0]['center_money']);
      for($i=0; $i<count($center_money) ;$i++){
        $last_ver = Func::get_last_version(date('Y')+543,$center_money[$i]['center_money']);
        $view[$i] = Estimate::select(DB::raw('status, version,fund_center,center_money,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',date('Y')+543)
          ->where('version',$last_ver)
          ->where('center_money',$center_money[$i]['center_money'])
          ->groupBy('status' ,'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->get()->toArray();
      }
      // dd($view);

      return view('view_all',['views' => $view]);
    }

    public function post_approve(Request $request)
    {
      // เขต/ฝ่าย Approve
// dd(Carbon::now());
      if(Auth::user()->type == 5){
        foreach($request->approve2 as $key => $val){
          $arr = explode("-",$val);
            $last_ver = Func::get_last_version(date('Y')+543,$arr[1]);
            // dd($last_ver);
            if($request->btn == "true"){
              $update = DB::table('estimates')
                ->where('stat_year', date('Y')+543)
                ->where('account', $arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 1 ,'budget'=> $request->new2[$arr[0]][$arr[1]],'approve_by1' => Auth::user()->emp_id ,'updated_at' => Carbon::now()]);
                $approve = new Approve_log;
                $approve->user_approve = Auth::user()->emp_id;
                $approve->stat_year = date('Y')+543;
                $approve->version = $last_ver;
                $approve->center_money = $arr[1];
                $approve->save();
                $msg = 'อนุมัติสำเร็จ';

            }elseif($request->btn == "false"){
              $update = DB::table('estimates')
                ->where('stat_year', date('Y')+543)
                ->where('account',$arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 3,'approve_by1' => NULL]);
                $msg = 'ยกเลิกอนุมัติสำเร็จ';
            }

        }
      }
      // dd($request->all());
      if(Auth::user()->type == 4 || Auth::user()->type == 1){
        foreach($request->approve1 as $key => $val){
          $arr = explode("-",$val);
          // for ($i=0 ; $i<count($arr) ; $i++) {
            // dd($arr[1]);
            $last_ver = Func::get_last_version(date('Y')+543,$arr[1]);
            if($request->btn == "true"){
              $update = DB::table('estimates')
                ->where('stat_year', date('Y')+543)
                ->where('account', $arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 0 ,'budget'=> $request->new1[$arr[0]][$arr[1]],'approve_by1' => Auth::user()->emp_id ,'updated_at' => Carbon::now()]);
                $approve = new Approve_log;
                $approve->user_approve = Auth::user()->emp_id;
                $approve->stat_year = date('Y')+543;
                $approve->version = $last_ver;
                $approve->center_money = $arr[1];
                $approve->save();
                $msg = 'อนุมัติสำเร็จ';
            }elseif($request->btn == "false"){
              $update = DB::table('estimates')
                ->where('stat_year', date('Y')+543)
                ->where('account',$arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 4,'approve_by1' => NULL,'updated_at' => Carbon::now()]);
                $msg = 'ยกเลิกอนุมัติสำเร็จ';

            }

        }
      }
      if($update){
        return back()->with('success', $msg);
      }

    }


//     public function post_approve(Request $request)
//     {
// // เขต/ฝ่าย Approve
// dd($request->all());
//       if(Auth::user()->type == 4 || Auth::user()->type == 1){
//         for($i=0 ;$i<count($request->approve1) ;$i++){
//           $arr = explode("-",$request->approve1[$i]);
//           $last_ver = Func::get_last_version(date('Y')+543,$arr[1]);
//           if($request->btn == "true"){
//             $update = DB::table('estimates')
//               ->where('stat_year', date('Y')+543)
//               ->where('account', $arr[0])
//               ->where('version',$last_ver)
//               ->where('center_money',$arr[1])
//               ->update(['status' => 0,'approve_by1' => Auth::user()->emp_id]);
//
//             $approve = new Approve_log;
//             $approve->user_approve = Auth::user()->emp_id;
//             $approve->stat_year = date('Y')+543;
//             $approve->version = $last_ver;
//             $approve->center_money = $arr[1];
//             $approve->save();
//             $msg = 'อนุมัติสำเร็จ';
//
//           }elseif($request->btn == "false"){
//             // dd(121212);
//             $update = DB::table('estimates')
//               ->where('stat_year', date('Y')+543)
//               ->where('account', $arr[0])
//               ->where('version',$last_ver)
//               ->where('center_money',$arr[1])
//               ->update(['status' => 4,'approve_by1' => NULL]);
//             $msg = 'ยกเลิกการอนุมัติแล้ว';
//           }
//         }
//         if($update){
//           return back()->with('success', $msg);
//         }
//       }
// // วง Approve
//     if(Auth::user()->type == 5){
//       for($i=0 ;$i<count($request->approve2) ;$i++){
//         $arr = explode("-",$request->approve2[$i]);
//         // var_dump($arr);
//         $last_ver = Func::get_last_version(date('Y')+543,$arr[1]);
//           if($request->btn == "true"){
//             // dd($last_ver);
//             $update = DB::table('estimates')
//               ->where('stat_year', date('Y')+543)
//               ->where('account', $arr[0])
//               ->where('version',$last_ver)
//               ->where('center_money',$arr[1])
//               ->update(['status' => 1,'approve_by2' => Auth::user()->emp_id,'updated_at' => Carbon::now()]);
//
//             $approve = new Approve_log;
//             $approve->user_approve = Auth::user()->emp_id;
//             $approve->stat_year = date('Y')+543;
//             $approve->version = $last_ver;
//             $approve->center_money = $arr[1];
//             $approve->save();
//             $msg = 'อนุมัติสำเร็จ';
//
//           }elseif($request->btn == "false"){
//             $update = DB::table('estimates')
//               ->where('stat_year', date('Y')+543)
//               ->where('account', $arr[0])
//               ->where('version',$last_ver)
//               ->where('center_money',$arr[1])
//               ->update(['status' => 3,'approve_by2' => NULL]);
//
//                 $msg = 'ยกเลิกการอนุมัติแล้ว';
//             }
//           }
//           if($update){
//           return back()->with('success', $msg);
//           }
//         }
//   }
    public function get_struc()
    {
      $data = Structure::get();
      return view('import_struc',['data' => $data]);
    }
    public function post_struc(Request $request)
    {
      // dd(3333);
      // import file ข้อมูลโครงสร้าง
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
     $insert_log->path = $pathreal.$name;
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
       return back()->with('success', 'บันทึกข้อมูลแล้ว');
     }
    }

    public function edit_struc(Request $request)
    {
      // แก้ไขข้อมูลโครงสร้างองค์กร
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
      // ลบข้อมูลโครงสร้างองค์กร
      $delete = Structure::find($request->id);
      $delete->delete();

      if($delete){
        return back()->with('success', 'ลบข้อมูลแล้ว');
      }
    }

    public function post_add_struc(Request $request)
    {
      // เพิ่มข้อมูลโครงสร้างองค์กร
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
      // export template to sap
      $sap = Estimate::select('stat_year','version','fund_center','account',DB::raw('SUM(budget) as budget'))
        ->where('stat_year', $request->stat_year)
        ->where('status',1)
        ->groupBy('stat_year','version','fund_center','account')
        ->get()->toArray();
        // dd($sap);
        foreach ($sap as $key => $value) {
              $data[] = array(
                'year' => $value['stat_year']-543,
                'from' => 1,
                'to' => 1,
                'version' => $value['version'],
                'fund' => 'oper',
                'func.a' => 'z0',
                'fund_center' => $value['fund_center'],
                'cmmt' => $value['account'],
                'name' => Func::get_account($value['account']),
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
      // ขั้นตอนงบประมาณ
      if(Auth::user()->type == 5 || Auth::user()->type == 1 || Auth::user()->type == 4){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('fund_center',Auth::user()->fund_center)->groupBy('center_money')->orderBy('center_money','ASC')->get();
      }else{
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      }
      // dd($center);
      if($center->count()){
        // dd(9999);
        foreach($center as $data){
          // var_dump($data->center_money);
          $last_ver = Func::get_last_version(date('Y')+543,$data->center_money);
    // dd($last_ver);
          $get_status[] = Estimate::select('stat_year','cost_title','center_money','status',DB::raw('SUM(budget) as budget'))
          ->where('center_money',$data->center_money)
          ->where('stat_year',date('Y')+543)
          ->where('version',$last_ver)
          ->groupBy('status','cost_title','center_money','stat_year')
          ->get()->toArray();
        }
  // dd($get_status);
      }else{
        $firstcen = NULL;
        $center = NULL;
        $get_status = NULL;
      }

      // dd($get_status);

// dd($status);
      return view('status_report',['status' => $get_status,'year' => date('Y')+543]);
    }

    public function post_status(Request $request)
    {
      // วง สามารถค้นหาชื่อฝ่ายได้
      $get_status = NULL;
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('cost_title','like','%'.$request->cost_title.'%')->groupBy('center_money')->orderBy('center_money','ASC')->get();
      }
// dd($center);
    foreach($center as $data){
      $first = $data->center_money;
      $last_ver = Func::get_last_version(date('Y')+543,$first);
// dd($last_ver);
      $get_status[] = Estimate::select('stat_year','cost_title','center_money','status',DB::raw('SUM(budget) as budget'))
      ->where('center_money',$first)
      ->where('stat_year',date('Y')+543)
      ->where('version',$last_ver)
      ->groupBy('status','cost_title','center_money','stat_year')
      ->get()->toArray();
    }

        // dd($get_status);

      return view('status_report',['status' => $get_status]);
    }

    public function get_version()
    {
      // ดูversionงบประมาณ
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
      // ค้นหางบประมาณversionต่าง
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
      // เปรียบเทียบข้อมูลงบประมาณ
      if(Auth::user()->type == "5" || Auth::user()->type == "1"){
        // dd(24);

        $this->validate($request, [
          'center_money' => 'required',
          'fund_center' => 'required'
        ]);
        $last_ver = Func::get_last_version(date('Y')+543,$request->center_money);
        // dd($last_ver);
        $last_ver_old = Func::get_last_version(date('Y')+542,$request->center_money);
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money','like','%'.$request->center_money.'%')
          ->where('fund_center','like','%'.$request->fund_center.'%')
          ->where('stat_year',date('Y')+543)
          ->where('version',$last_ver)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money','like','%'.$request->center_money.'%')
          ->where('fund_center','like','%'.$request->fund_center.'%')
          ->where('stat_year',date('Y')+542)
          ->where('version',$last_ver_old)
          ->groupBy('account','stat_year')->get();
          // dd($view);
      }else{
        $last_ver = Func::get_last_version(date('Y')+543,Auth::user()->center_money);
        $last_ver_old = Func::get_last_version(date('Y')+542,Auth::user()->center_money);
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',date('Y')+543)
          ->where('version',$last_ver)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',date('Y')+542)
          ->where('version',$last_ver_old)
          ->groupBy('account','stat_year')->get();
      }
      // dd($old);
      $data =[];
      $data_old =[];
      foreach($old as $value_old){
        $data_old[$value_old->account][$value_old->stat_year] = $value_old->budget;
      }
      foreach($view as $value){
        $data[$value->account][$value->stat_year] = $value->budget;
      }
      // dd($data);
      return view('view_estimate',['data' => $data,'data_old' => $data_old]);
    }

    public function print_all(Request $request)
    {
      // export file ข้อมูลการของบประมาณ(หน้า report_apv)
      // dd(Auth::user()->type);
      if(Auth::user()->type == 1 || Auth::user()->type == 5){
        $center_money = Estimate::select('center_money')->where('stat_year',$request->year)->where('cost_title','like','%'.$request->cost_title.'%')->where('center_money','like','%'.$request->center_money.'%')->groupBy('center_money')->get();
      }else{
        if(substr($request->center_money,5) == "00"){
          $center_money = Estimate::select('center_money')->where('stat_year',$request->year)->where('fund_center',Auth::user()->fund_center)->groupBy('center_money')->get();
        }elseif(Auth::user()->type == 4){
          $center_money = Estimate::select('center_money')->where('stat_year',$request->year)->where('fund_center',Auth::user()->fund_center)->where('center_money','like','%'.$request->center_money.'%')->groupBy('center_money')->get();

        }else{
          $center_money = Estimate::select('center_money')->where('stat_year',$request->year)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();

        }
      }
      // dd($center_money);
      if($center_money->count()){
        foreach($center_money as $data){
          $last_ver = Func::get_last_version($request->year,$data->center_money);
          $view[] = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
            ->where('stat_year',$request->year)
            ->where('version',$last_ver)
            ->where('center_money',$data->center_money)
            ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
            ->get()->toArray();
        }
      }
      // dd($view);
        $datas[]  = array('year' => 'ปีงบประมาณ' ,'center' => 'ศูนย์ต้นทุน','account' => 'หมวดค่าใช้จ่าย','name' => 'รายการภาระผูกพัน','amount' => 'งบประมาณ' ,'status' => 'สถานะ');
// dd($datas);
      foreach ($view as $key => $arr_value) {
        foreach($arr_value as $key2 => $value){
          // dd($value['status']);
          if($value['status'] == "0"){
            $status = 'ฝ่าย/เขต อนุมัติแล้ว';
          }elseif($value['status'] == "1"){
            $status = 'งบประมาณอนุมัติแล้ว';
          }elseif($value['status'] == "5"){
            $status = 'งบประมาณรอพิจารณา';
          }elseif($value['status'] == "4"){
            $status = 'แก้ไขงบประมาณ';
          }elseif($value['status'] == "3"){
            $status = 'วง.ขอแก้ไขงบ';
          }

          $datas[] = array(
            'year' => $value['stat_year'],
            'center' => $value['center_money'],
            'account' => $value['account'],
            'name' => Func::get_account($value['account']),
            'amount' => $value['budget'],
            'status' => $status
          );
        }
      }
      // dd($data);
      // // dd($datas);
      // if(!isset($data)){
      //   dd('ไม่มีข้อมูล');
      // }
      // // dd($data);
      Excel::create('View Estimate '.$request->year,function($excel) use ($datas){
        $excel->setTitle('Estimate');
        $excel->sheet('Estimate',function($sheet) use ($datas){
          $sheet->fromArray($datas,null,'A1',false,false);
        });
      })->download('xlsx');
    }

    public function get_approve()
    {
// dd(343);
    // วง/ฝ่าย approve งบ
      $center_money = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();

      // dd($center_money);
      if($center_money->count()){
        foreach($center_money as $data){
          $last_ver = Func::get_last_version(date('Y')+543,$data->center_money);
          $view[] = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
            ->where('stat_year',(date('Y')+543))
            ->where('version',$last_ver)
            ->where('center_money',$data->center_money)
            ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
            ->get()->toArray();
        }

      }else{
        $last_ver = NULL;
        $view = NULL;
      }

      if(substr(Auth::user()->center_money,5) == "00" && strpos(Func::get_cost_title(Auth::user()->center_money),'ฝ') == 0){
        $cost = mb_substr(Func::get_cost_title(Auth::user()->center_money),1,3,'UTF-8');
        // dd(123);
      }else{
        $cost = Func::get_cost_title(Auth::user()->center_money);
      }
      return view('report_apv',['views' => $view ,'yy' => date('Y')+543, 'centermoney' => Auth::user()->center_money,'cost' => $cost]);

    }
    public function post_report_apv(Request $request)
    {
      // dd($request->all());
      // หน้า report apv
      if(Auth::user()->type == 1 || Auth::user()->type == 5){
        $this->validate($request, [
          'cost_title'  => 'required'
        ]);
          $center_money = Estimate::select('center_money')->where('stat_year',$request->stat_year)->where('center_money','like','%'.$request->center_money.'%')->where('cost_title','like','%'.$request->cost_title.'%')->groupBy('center_money')->get();

      }else{
        if(substr(Auth::user()->center_money,5) == "00" || Auth::user()->type == "4"){
          $center_money = Estimate::select('center_money')->where('stat_year',$request->stat_year)->where('center_money','like','%'.$request->center_money.'%')->where('fund_center',Auth::user()->fund_center)->groupBy('center_money')->get();
        }else{
          $center_money = Estimate::select('center_money')->where('stat_year',$request->stat_year)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
        }
      }
      // dd($center_money);
      if($center_money->count()){
        foreach($center_money as $data){
          $last_ver = Func::get_last_version($request->stat_year,$data->center_money);
          $view[] = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
            ->where('stat_year',$request->stat_year)
            ->where('version',$last_ver)
            ->where('center_money',$data->center_money)
            ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
            ->get()->toArray();
        }
        // dd($view);
      }else{
        $last_ver = NULL;
        $view = NULL;
      }

      return view('report_apv',['views' => $view ,'yy' => $request->stat_year, 'centermoney' => $request->center_money,'cost' =>$request->cost_title]);

    }

    public function get_compare()
    {
      return view('report_compare',['yy' => date('Y')+543, 'fund' => NULL ,'center' => NULL ,'account' => NULL]);
    }

    public function post_compare(Request $request)
    {
      // dd($request->all());
      // หน้า report compare
      $name = DB::table('masters')
        ->whereNull('deleted_at')
         ->whereNotIn('account', function($query)
         {
             $query->select('account')
                   ->from('estimates')
                   ->where('stat_year',date('Y')+543)
                   ->whereNull('deleted_at')
                   ->where('version', Func::get_last_version(date('Y')+543 ,Auth::user()->center_money))
                   ->where('center_money',Auth::user()->center_money)
                   ->groupBy('account');
         })->get();
         foreach ($name as $key => $value) {
           $data_old[$value->account][date("Y",strtotime("-1 year"))+543] = 0;
           $data[$value->account][date("Y")+543] = 0;
         }

      if(Auth::user()->type == "5" || Auth::user()->type == "1"){
        $this->validate($request, [
          'center_money' => 'required',
          'fund_center' => 'required'
        ]);
        $id = Func::get_idcostname($request->fund_center,$request->center_money);
        // dd($last_ver);
        if($id != NULL){
          $last_ver = Func::get_last_version($request->stat_year,$id->CostCenterID);
          // dd($last_ver);
          $last_ver_old = Func::get_last_version(($request->stat_year-1),$id->CostCenterID);
          $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('account','like','%'.$request->account.'%')
            ->where('cost_name','like','%'.$request->center_money.'%')
            ->where('cost_title','like','%'.$request->fund_center.'%')
            ->where('stat_year',$request->stat_year)
            ->where('version',$last_ver)
            ->groupBy('account','stat_year')->get();
  // dd($last_ver);
          $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('account','like','%'.$request->account.'%')
            ->where('cost_name','like','%'.$request->center_money.'%')
            ->where('cost_title','like','%'.$request->fund_center.'%')
            ->where('stat_year',($request->stat_year-1))
            ->where('status',1)
            ->where('version',$last_ver_old)
            ->groupBy('account','stat_year')->get();
            $fund = $id->CostCenterID;
            $center = $id->CostCenterID;
        }else{
          $fund = NULL;
          $center = NULL;
          $view =[];
          $old =[];
        }

      }else{
        $last_ver = Func::get_last_version($request->stat_year,Auth::user()->center_money);
        $last_ver_old = Func::get_last_version(($request->stat_year-1),Auth::user()->center_money);
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',$request->stat_year)
          ->where('version',$last_ver)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',($request->stat_year-1))
          ->where('version',$last_ver_old)
          ->where('status',1)
          ->groupBy('account','stat_year')->get();
          $fund = Auth::user()->fund_center;
          $center = Auth::user()->center_money;
      }
      // dd($old);
      $data =[];
      $data_old =[];
      foreach($old as $value_old){
        $data_old[$value_old->account][$value_old->stat_year] = $value_old->budget;
      }
      foreach($view as $value){
        $data[$value->account][$value->stat_year] = $value->budget;
      }

      $datas = array_merge_recursive($data_old,$data);
      // dd($datas);

      // return Redirect::route('post_compare',array('data' => $datas,'data_old' => $data_old,'account'=>$request->account ,'fund'=> $fund,'yy' => $request->stat_year, 'center' => $center))->withInput();
// session()->flashInput($request->all());
      return view('report_compare',['data' => $datas,'data_old' => $data_old,'account'=>$request->account ,'fund'=> $fund,'yy' => $request->stat_year, 'center' => $center]);

    }

    public function print_compare(Request $request)
    {
      // export file compare
      // dd($request->all());
      $name = DB::table('masters')
        ->whereNull('deleted_at')
         ->whereNotIn('account', function($query)
         {
             $query->select('account')
                   ->from('estimates')
                   ->where('stat_year',date('Y')+543)
                   ->whereNull('deleted_at')
                   ->where('version', Func::get_last_version(date('Y')+543 ,Auth::user()->center_money))
                   ->where('center_money',Auth::user()->center_money)
                   ->groupBy('account');
         })->get();
         foreach ($name as $key => $value) {
           $data_old[$value->account][date("Y",strtotime("-1 year"))+543] = 0;
           $data[$value->account][date("Y")+543] = 0;
         }
      if(Auth::user()->type == "5" || Auth::user()->type == "1"){
        $id = Func::get_idcostname($request->fundcenter,$request->centermoney);
        if($id != NULL){
          $last_ver = Func::get_last_version($request->statyear,$id->CostCenterID);
          // dd($last_ver);
          $last_ver_old = Func::get_last_version(($request->statyear-1),$id->CostCenterID);
          $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('account','like','%'.$request->account.'%')
            ->where('cost_name','like','%'.$request->centermoney.'%')
            ->where('cost_title','like','%'.$request->fundcenter.'%')
            ->where('stat_year',$request->statyear)
            ->where('version',$last_ver)
            ->groupBy('account','stat_year')->get();

          $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('account','like','%'.$request->account.'%')
            ->where('cost_name','like','%'.$request->centermoney.'%')
            ->where('cost_title','like','%'.$request->fundcenter.'%')
            ->where('stat_year',($request->statyear-1))
            ->where('version',$last_ver_old)
            ->groupBy('account','stat_year')->get();
            // dd($view);
            $fund = $id->CostCenterID;
            $center = $id->CostCenterID;
        }else{
          $fund = NULL;
          $center = NULL;
          $view =[];
          $old =[];
        }

      }else{
        $last_ver = Func::get_last_version($request->statyear,Auth::user()->center_money);
        $last_ver_old = Func::get_last_version(($request->statyear-1),Auth::user()->center_money);
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',$request->statyear)
          ->where('version',$last_ver)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('account','like','%'.$request->account.'%')
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',($request->statyear-1))
          ->where('version',$last_ver_old)
          ->groupBy('account','stat_year')->get();
          $fund = Auth::user()->fund_center;
          $center = Auth::user()->center_money;
      }
      // dd($view);
      $data =[];
      $data_old =[];
      foreach($old as $value_old){
        $data_old[$value_old->account][$value_old->stat_year] = $value_old->budget;
      }
      foreach($view as $value){
        $data[$value->account][$value->stat_year] = $value->budget;
      }
      $data = array_merge_recursive($data_old,$data);
      $datas[]  = array('account' => 'บัญชีรายการภาระผูกพัน' ,'name' => 'รายการภาระผูกพัน','fund' => 'ฝ่าย','center' => 'ส่วน','before' => 'ปีงบประมาณ '.($request->statyear-1) ,'now' => 'ปีงบประมาณ '.$request->statyear);
// dd($datas);
      foreach ($data as $key => $arr_value) {
        if(isset($data[$key][$request->statyear-1])){
          $before = $data[$key][$request->statyear-1];
        }else{
          $before = '-';
        }
        if(isset($data[$key][$request->statyear])){
          $now = $data[$key][$request->statyear];
        }else{
          $now = '-';
        }
          $datas[] = array(
            'account' => $key,
            'name' => Func::get_account($key),
            'fund' => Func::get_cost_title($fund),
            'center' => Func::get_name_costcenter($center),
            'before' => $before,
            'now' => $now
          );
      }
      // dd($datas);
      Excel::create('View Estimate Compare'.$request->year,function($excel) use ($datas){
        $excel->setTitle('Compare');
        $excel->sheet('Compare',function($sheet) use ($datas){
          $sheet->fromArray($datas,null,'A1',false,false);
        });
      })->download('xlsx');
    }
}
