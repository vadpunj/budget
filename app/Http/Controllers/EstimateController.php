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
use App\Cmmt;
use DB;
use Func;
use App\Log_user;
use Excel;
use PDF;
use Carbon\Carbon;

class EstimateController extends Controller
{
    public function get_add()
    {
      $year3=[];
      $year2=[];
      $year1=[];
      $now=[];
      $all_now=[];
      $status=[];
      $reason=[];
      $stage=6;

      // query หาชื่อค่าใช้จ่ายทั้งหมด
      $name = DB::table('masters')
        ->whereNull('deleted_at')
        ->get()->toArray();
// dd($test);
       foreach ($name as $key => $value) {
         $year3[(Func::get_year()-3)][$value->account] = 0;
         $year2[(Func::get_year()-2)][$value->account] = 0;
         $year1[(Func::get_year()-1)][$value->account] = 0;
         $all_name[$value->id1][$value->id2][Func::get_year()][$value->account] = 0;
         $now[Func::get_year()][$value->account] = NULL;
         $status[Func::get_year()][$value->account] = 5;
         $reason[Func::get_year()][$value->account] = NULL;
       }
       // dd($head[1]);
       // query ค่าใช้จ่ายงบประมาณที่อนุมัติแล้วในปีก่อนย้อนหลังไป3ปี
        for($i = (Func::get_year()-3) ; $i <= (Func::get_year()-1) ; $i++){
          $last_ver = Func::get_last_version($i ,Auth::user()->center_money);

          if($last_ver != NULL){
            $list = DB::table('estimates')
              ->select('stat_year','id1','reason','account','status', DB::raw('SUM(budget) as budget'))
              ->where('stat_year',$i)
              ->whereNull('deleted_at')
              ->where('version', $last_ver)
              ->where('center_money',Auth::user()->center_money)
              ->where('status',1)
              ->groupBy('stat_year','id1','reason','account','version','status')
              ->orderBy('id1','DESC')
              ->get()->toArray();
              // dd($list);
              foreach ($list as $key => $value) {
                if ($value->stat_year == (Func::get_year()-3)) {
                  $year3[$value->stat_year][$value->account] = $value->budget;
                }if ($value->stat_year == (Func::get_year()-2)) {
                  $year2[$value->stat_year][$value->account] = $value->budget;
                }if($value->stat_year == (Func::get_year()-1)){
                  $year1[$value->stat_year][$value->account] = $value->budget;
                }
              }

          }else{
            $list = NULL;
          }
        }
        // dd($year3);
        // query ข้อมูลงบปีปัจจุบัน
        $last_ver_now = Func::get_last_version(Func::get_year(),Auth::user()->center_money);
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','reason','account','status', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('version', $last_ver_now)
          ->where('center_money',Auth::user()->center_money)
          ->groupBy('stat_year','id1','reason','account','version','status')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
            $status[$value->stat_year][$value->account] = $value->status;
            $reason[$value->stat_year][$value->account] = $value->reason;
            $stage = $value->status;
          }
          // dd(count($list_now));
          $lists = Cmmt::get();
          foreach($lists as $value){
            $head[$value->name_id] = $value->name;
          }
 // dd($year1);
// if(!empty($year1['2563']['5X601102'])){
//   dd('have');
// }else{
//   dd('dont have');
// }
// dd($status);
      return view('add_est',['stage' => $stage,'list_now' => $list_now,'name' => $all_name,'now' => $now,'status' => $status , 'reason' => $reason,'year3' => $year3 ,'year2' => $year2, 'year1' => $year1 ,'cate' => 1 , 'head' => $head]);
    }

    public function post_add(Request $request)
    {
      // dd($request->all());
      // บันทึกข้อมูลมูลงบประมาณ
      // เก็บข้อมูลUserที่ของบในtable User_request
      if($request->type == 'save'){
        $last = Func::get_last_version(Func::get_year(),Auth::user()->center_money);
        // dd($last);
        if(!is_null($last)){
          $update = DB::table('estimates')->where('stat_year',Func::get_year())->where('center_money',Auth::user()->center_money)->where('version','<=',$last)->update(['status_ver' => 0]);
        }
        $insert = [];
        foreach ($request->budget as $key => $val) {
          // dd($last+1);
          if(!is_null($val)){
            $insert = new Estimate;
            $insert->version = $last+1;
            $insert->stat_year = Func::get_year();
            $insert->account = $key;
            $id = Func::list_cmmt($key);
            $insert->id1 = $id[0]->id1;
            $insert->id2 = $id[0]->id2;
            $insert->budget = $val;
            $insert->status = 6;
            $insert->status_ver = 0;
            $insert->center_money = Auth::user()->center_money;
            $insert->fund_center = Auth::user()->fund_center;
            $insert->div_center = Auth::user()->division_center;
            // $insert->fund_center = Auth::user()->fund_center;
            $insert->cost_title = Auth::user()->cost_title;
            // $insert->cost_title = Func::get_cost_title(Auth::user()->center_money);
            $insert->cost_name = Auth::user()->part;
            $insert->reason = $request->reason[$key];
            $insert->created_by = Auth::user()->emp_id;
            $insert->save();
          }
        }
      }else{
        $last = Func::get_last_version(Func::get_year(),Auth::user()->center_money);
        // dd($last);
        if(!is_null($last)){
          $update = DB::table('estimates')->where('stat_year',Func::get_year())->where('center_money',Auth::user()->center_money)->where('version','<=',$last)->update(['status_ver' => 0]);
        }
        foreach ($request->budget as $key => $val) {
          // dd($last+1);
          if(!is_null($val)){
            $insert = new Estimate;
            $insert->version = $last+1;
            $insert->stat_year = Func::get_year();
            $insert->account = $key;
            $id = Func::list_cmmt($key);
            $insert->id1 = $id[0]->id1;
            $insert->id2 = $id[0]->id2;
            $insert->budget = $val;
            $insert->status = 5;
            $insert->status_ver = 1;
            $insert->center_money = Auth::user()->center_money;
            $insert->fund_center = Auth::user()->fund_center;
            $insert->div_center = Auth::user()->division_center;
            // $insert->fund_center = Auth::user()->fund_center;
            $insert->cost_title = Auth::user()->cost_title;
            // $insert->cost_title = Func::get_cost_title(Auth::user()->center_money);
            $insert->cost_name = Auth::user()->part;
            $insert->reason = $request->reason[$key];
            $insert->created_by = Auth::user()->emp_id;
            $insert->save();
          }
        }
      }


      $data = new User_request;
      $data->stat_year = Func::get_year();
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = Auth::user()->part;
      $data->name =  Auth::user()->emp_id;
      $data->phone = Auth::user()->tel;
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
      // dd(5345);
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

     // Master::delete();
     // DB::table('masters')->delete();
     $key_name = ['account','name'];
// dd($data->toArray());
    foreach($data->toArray() as $value){
      // dd($value['รายการภาระผูกพัน']);
      if($value['name_id']){
        $insert = new Master;
        $insert->account = trim($value['name_id']);
        $insert->name = $value['name'];
        $insert->id1 = $value['id1'];
        $insert->id2 = $value['id2'];
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
      $list = Cmmt::get();
      return view('master',['data' => $data, 'list' => $list,'cate' => 1]);
    }

    public function add_master(Request $request)
    {
      // dd('234');
      // บันทึกข้อมูล master
      $this->validate($request, [
        'account' => 'required',
        'name'  => 'required'
      ]);

      $insert = new Master;
      $insert->account = trim($request->account);
      $insert->name = $request->name;
      $insert->id1 = $request->id1;
      $insert->id2 = $request->id2;
      $insert->save();

      $data = Master::get();
      $list = Cmmt::get();

      if($insert){
        return view('master',['data' => $data, 'list' => $list,'cate' => 1])->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }
    public function post_master(Request $request)
    {
      $find = Master::where('id1',$request->id1)->get();
      foreach($find as $value){
        $data_arr[$value->id1][$value->id2][$value->account] = $value->name;
      }
      // dd($data_old);
      $list = Cmmt::get();
      $data = Master::get();
      return view('master',['data' => $data,'data_arr' => $data_arr, 'list' => $list , 'cate' => $request->id1]);
    }

    public function post_edit_master(Request $request)
    {
      // dd($request->id);
      $this->validate($request, [
        'account' => 'required|min:8',
        'name'  => 'required'
      ]);
      $update = DB::table('masters')->where('account',$request->account)->update(['name' => $request->name]);
      $data = Master::get();
      $list = Cmmt::get();

        // dd(231);
      return view('master',['data' => $data, 'list' => $list,'cate' => 1])->with('success','บันทึกข้อมูลแล้ว');

    }

    public function post_delete_master(Request $request)
    {
      $delete = DB::table('masters')->where('account', $request->account)->delete();
      $data = Master::get();
      $list = Cmmt::get();

        return view('master',['data' => $data, 'list' => $list,'cate' => 1])->with('success', 'ลบข้อมูลแล้ว');

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

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();

     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $pathreal.$name;
     $insert_log->type_log = 'งบทำการ';
     $insert_log->save();
     $last = Func::get_last_version($data->toArray()[0]['ปีงบ'] ,$data->toArray()[0]['ศูนย์ต้นทุน']);
     if(!is_null($last)){
       $update = DB::table('estimates')->where('stat_year',$data->toArray()[0]['ปีงบ'])->where('center_money',$data->toArray()[0]['ศูนย์ต้นทุน'])->where('version','<=',$last)->update(['status_ver' => 0]);
     }
     $data_list = [];
    foreach($data->toArray() as $value){

      $id = Func::list_cmmt($value['บัญชี']);
      $insert = [
                'version'  =>  ($last+1),
                'stat_year' =>  Func::get_year(),
                'account'   =>  $value['บัญชี'],
                'id1'       =>  $id[0]->id1,
                'id2'       =>  $id[0]->id2,
                'budget'    =>  $value['เงิน'],
                'status'    =>  5,
                'status_ver'=>  1,
                'center_money'=> $value['ศูนย์ต้นทุน'],
                'fund_center'=> $value['ศูนย์เงินทุน'],
                'div_center'=>  $value['สายงาน'],
                'cost_title'=>  Func::get_cost_title($value['ศูนย์ต้นทุน']),
                'cost_name' =>  Func::get_name_costcenter($value['ศูนย์ต้นทุน']),
                'reason'    =>  $value['คำอธิบาย'],
                'created_by'=>  Auth::user()->emp_id,
                'created_at'=>  Carbon::now(),
                'updated_at'=>  Carbon::now()
              ];

      $data_list[] = $insert;
    }
      $data_list = collect($data_list);

      $chunks = $data_list->chunk(10);

      foreach ($chunks as $chunk)
      {
         $dd = DB::table('estimates')->insert($chunk->toArray());
      }


      // if($value['ปีงบ']){
      //  $insert = new Estimate;
      //  $insert->stat_year = $value['ปีงบ'];
      //  $insert->version = $last+1;
      //  $insert->account = trim($value['บัญชี']);
      //  $id = Func::list_cmmt($value['บัญชี']);
      //  if($id->isEmpty()){
      //    return back()->with('success', 'กรุณาเพิ่มรายการบัญชี');
      //  }
      //  $insert->id1 = $id[0]->id1;
      //  $insert->id2 = $id[0]->id2;
      //  $insert->budget = $value['เงิน'];
      //  $insert->center_money = $value['ศูนย์ต้นทุน'];
      //  $insert->fund_center = $value['ศูนย์เงินทุน'];
      //  $insert->div_center = $value['สายงาน'];
      //  $insert->cost_title = Func::get_cost_title($value['ศูนย์ต้นทุน']);
      //  $insert->cost_name = trim(Func::get_name_costcenter($value['ศูนย์ต้นทุน']));
      //  $insert->status = 5;
      //  $insert->status_ver = 1;
      //  $insert->reason = $value['คำอธิบาย'];
      //  $insert->created_by = Auth::user()->emp_id;
      //  $insert->save();
      // }
       if($dd){
         return back()->with('success', 'บันทึกข้อมูลแล้ว');
       }
    }

    public function post_edit_account(Request $request)
    {
      // dd(2321);
      // แก้ไขงบบัญชีงบที่แมพไม่เจอ
      $update = Estimate::find($request->id);
      $update->account = $request->account;
      $id = Func::list_cmmt($request->account);
      if($id->isEmpty()){
        return back()->with('success', 'กรุณาเพิ่มรายการบัญชี');
      }
      $update->id1 = $id[0]->id1;
      $update->id2 = $id[0]->id2;
      $update->update();

      if($update){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
    }
    public function get_view()
    {
      $bg = null;
      $cmmt = Cmmt::get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $first = $str->first()->FundsCenterID;

      // dd($first);
      return view('view_all',['bg' => $bg,'cmmt' => $cmmt ,'cat_cm' => 1,'str' => $str ,'divid' => $first]);
    }
    public function find_fundcenter(Request $request)
    {
// SELECT FundID FROM `structures` where FundsCenterID = '1n00000' and FundID is not null group by FundID
// SELECT * FROM `structures` where FundsCenterID = '1B00000' and CostCenterID is null and FundID is not null
// select FundID,CostCenterName FROM structures where FundsCenterID = '1R00000' and FundID is not null and (CostCenterName like 'ฝ่าย%' or CostCenterName like 'สำนักงาน%') group by FundID,CostCenterName
      $divid = $request->id;
      $data = Structure::select('FundID','CostCenterName')
      ->where('FundsCenterID',$divid)
      ->whereNull('CostCenterID')
      ->whereNotNull('FundID')
      ->groupBy('FundID','CostCenterName')
      ->get();
      // return $name;
      // $data = Structure::select('FundID')
      // ->where('FundsCenterID',$divid)
      // ->whereNotNull('FundID')
      // ->groupBy('FundID')
      // ->get();
      // dd($cmmt);
      return response()->json($data);
    }

    public function find_center(Request $request)
    {
    // SELECT CostCenterID,CostCenterName FROM `structures` where FundID = '1A00100' and CostCenterID is not null group by CostCenterID,CostCenterName
      $fundid = $request->id;
      $data = Structure::select('CostCenterID','CostCenterName')
      ->where('FundID',$fundid)
      ->whereNotNull('CostCenterID')
      ->groupBy('CostCenterID','CostCenterName')
      ->get();
      // return $name;
      // $data = Structure::select('FundID')
      // ->where('FundsCenterID',$fundid)
      // ->whereNotNull('FundID')
      // ->groupBy('FundID')
      // ->get();
      // dd($cmmt);
      return response()->json($data);
    }

    public function post_view(Request $request)
    {
      // dd($request->all());
      // เฉพาะ วง ที่สามารถค้นหาตามชื่อฝ่ายได้
      $bg =null;
      $status[] =null;
      $reason[]=null;

      $cmmt = Cmmt::get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();

      if(Auth::user()->type == 5 || Auth::user()->type == 1 || Auth::user()->type == 6){
        $fun_center = $request->fund_id;
      }elseif(Auth::user()->type == 4){
        $fun_center = Auth::user()->fund_center;
      }

      if(Auth::user()->type == 5 || Auth::user()->type == 1 ||  Auth::user()->type == 4 || Auth::user()->type == 6){
// dd($fun_center);
        $view = Estimate::select(DB::raw('status,reason,center_money, id2 ,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->where('status_ver',1)
          ->where('status','!=',6)
          ->where('fund_center',$fun_center)
          ->where('id1',$request->id1)
          ->groupBy('status','center_money','reason','version','id2','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->orderBy('center_money','asc')
          ->orderBy('account','asc')
          ->get();
          // dd($view);
          if(!empty($view)){
            foreach($view as $key){
              $status[$key->id2][$key->account][$key->center_money] = $key->status;
              $bg[$key->id2][$key->account][$key->center_money] = $key->budget;
              $reason[$key->id2][$key->account][$key->center_money] = $key->reason;
            }
          }
      return view('view_all',['fundid'=> $fun_center ,'reason'=> $reason,'views' => $view,'bg' => $bg,'status' => $status, 'cat_cm' => $request->id1, 'divid' => $request->div_id ,'cmmt' => $cmmt,'str' => $str]);

      }else{
        // dd(232);
        $view = Estimate::select(DB::raw('status,reason,center_money, id2 ,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->where('status_ver',1)
          ->where('status','!=',6)
          ->where('center_money', Auth::user()->center_money)
          ->where('id1',$request->id1)
          ->groupBy('status','reason','center_money','version','id2','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->orderBy('center_money','asc')
          ->orderBy('account','asc')
          ->get();
          if(!empty($view)){
            foreach($view as $key){
              $status[$key->id2][$key->account][$key->center_money] = $key->status;
              $bg[$key->id2][$key->account][$key->center_money] = $key->budget;
              $reason[$key->id2][$key->account][$key->center_money] = $key->reason;
            }
          }
          return view('view_all',['reason'=> $reason,'views' => $view,'bg' => $bg,'status' => $status, 'cat_cm' => $request->id1, 'divid' => $request->div_id ,'cmmt' => $cmmt,'str' => $str]);

      }
// dd($reason);
// dd(1232);


// dd($name);
    }

    public function post_approve(Request $request)
    {
      // เขต/ฝ่าย Approve
// dd(Carbon::now());
// dd($request->all());
      if(Auth::user()->type == 5){
        foreach($request->approve2 as $key => $val){
          $arr = explode("-",$val);
            $last_ver = Func::get_last_version(Func::get_year(),$arr[1]);
            // dd($last_ver);
            if($request->btn == "true"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
                ->where('account', $arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 1 ,'budget'=> $request->new2[$arr[0]][$arr[1]],'approve_by2' => Auth::user()->emp_id ,'updated_at' => Carbon::now()]);
                $approve = new Approve_log;
                $approve->user_approve = Auth::user()->emp_id;
                $approve->stat_year = Func::get_year();
                $approve->version = $last_ver;
                $approve->center_money = $arr[1];
                $approve->save();
                $msg = 'อนุมัติสำเร็จ';

            }elseif($request->btn == "false"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
                ->where('account',$arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 3,'approve_by1' => NULL]);
                $msg = 'ยกเลิกอนุมัติสำเร็จ';
            }

        }
      }
      if(Auth::user()->type == 6){
        // dd($request->bg);
        foreach($request->approve3 as $key => $val){
            $arr = explode("-",$val);
            $last_ver = Func::get_last_version(Func::get_year(),$arr[1]);
            if($request->btn == "true"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
                ->where('account', $arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 2 ,'budget'=> $request->bg[$arr[0]][$arr[1]],'updated_at' => Carbon::now()]);
              $insert = new Export_estimate;
              $insert->version = 1;
              $insert->year = Func::get_year();
              $insert->div_center = $request->div;
              $insert->fund_center = $request->fund;
              $insert->account = $arr[0];
              // $insert->id1 =
              // $insert->id2 =
              $insert->budget = $request->bg[$arr[0]][$arr[1]];
              $insert->status = 1;
              // $insert->approve2 =
              $insert->approve_all = Auth::user()->emp_id;
              $insert->save();

              $approve = new Approve_log;
              $approve->user_approve = Auth::user()->emp_id;
              $approve->stat_year = Func::get_year();
              $approve->version = $last_ver;
              $approve->center_money = $arr[1];
              $approve->save();
              $msg = 'อนุมัติสำเร็จ';
            }elseif($request->btn == "false"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
                ->where('account',$arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 4,'approve_by1' => NULL,'updated_at' => Carbon::now()]);
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
            $last_ver = Func::get_last_version(Func::get_year(),$arr[1]);
            if($request->btn == "true"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
                ->where('account', $arr[0])
                ->where('version',$last_ver)
                ->where('center_money',$arr[1])
                ->update(['status' => 0 ,'budget'=> $request->new1[$arr[0]][$arr[1]],'approve_by1' => Auth::user()->emp_id ,'updated_at' => Carbon::now()]);
                $approve = new Approve_log;
                $approve->user_approve = Auth::user()->emp_id;
                $approve->stat_year = Func::get_year();
                $approve->version = $last_ver;
                $approve->center_money = $arr[1];
                $approve->save();
                $msg = 'อนุมัติสำเร็จ';
            }elseif($request->btn == "false"){
              $update = DB::table('estimates')
                ->where('stat_year', Func::get_year())
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
//     public function get_view_approve()
//     {
//       $bg = null;
//       $cmmt = Cmmt::get();
//       $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
//       $first = $str->first()->FundsCenterID;
//
//       // dd($first);
//       return view('approve',['bg' => $bg,'cmmt' => $cmmt ,'cat_cm' => 1,'str' => $str ,'divid' => $first]);
//     }
//     public function post_view_approve(Request $request)
//     {
//       $bg =null;
//       $status =null;
//       // $view =null;
// // dd($fun_center);
//
//         $view = Estimate::select(DB::raw('status, id2 ,fund_center,stat_year,account,sum(budget) as budget'))
//           ->where('stat_year',date('Y')+544)
//           ->whereIn('status',[0,1])
//           ->where('fund_center',$request->fund_id)
//           ->where('id1',$request->id1)
//           ->groupBy('status','version','id2','stat_year','fund_center','account')
//           ->orderBy('fund_center','asc')
//           ->orderBy('account','asc')
//           ->get();
//           if($view->count() != 0){
//             foreach($view as $key){
//               $status[$key->id2][$key->account] = $key->status;
//               $bg[$key->id2][$key->account] = $key->budget;
//             }
//           }else{
//             $view2 = Estimate::select(DB::raw('id2 ,fund_center,stat_year,account,sum(budget) as budget'))
//                 ->where('stat_year',date('Y')+544)
//                 ->where('status_ver',1)
//                 ->whereIn('status',[0,1])
//                 ->where('fund_center',$request->fund_id)
//                 ->where('id1',$request->id1)
//                 ->groupBy('version','id2','stat_year','fund_center','account')
//                 ->orderBy('fund_center','asc')
//                 ->orderBy('account','asc')
//                 ->get();
//                 if(!empty($view2)){
//                   foreach($view2 as $key){
//                     $status[$key->id2][$key->account] = 5;
//                     $bg[$key->id2][$key->account] = $key->budget;
//                   }
//                 }
//           }
//
// // dd($bg);
// // dd(1232);
//       $cmmt = Cmmt::get();
//       $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
//
// // dd($name);
//       return view('approve',['fundid'=> $request->fund_id,'bg' => $bg,'status' => $status, 'cat_cm' => $request->id1, 'divid' => $request->div_id ,'cmmt' => $cmmt,'str' => $str]);
//     }
    // public function approve_log(Request $request)
    // {
    //   // dd($request->all());
    //   if(Auth::user()->type == 5){
    //     foreach($request->approve2 as $key => $val){
    //       $arr = explode("-",$val);
    //         // dd($last_ver);
    //         if($request->btn == "true"){
    //            $insert = new Export_estimate;
    //            $insert->version = 1;
    //            $insert->year = date('Y')+544;
    //            $insert->div_center = $request->div_id;
    //            $insert->fund_center = $request->fund_id;
    //            $insert->account =  $arr[0];
    //            $insert->id1 =  $request->id1;
    //            $insert->id2 =  $request->id2;
    //            $insert->status = 0;
    //            $insert->budget = $request->new1[$arr[0]][$arr[1]];
    //            $insert->approve2 = Auth::user()->emp_id;
    //            $insert->save();
    //         }
    //     }
    //     if($insert){
    //       return back()->with('success', 'บันทึกข้อมูลแล้ว');
    //     }
    //   }
    //   // dd($request->all());
    //   if(Auth::user()->type == 6 || Auth::user()->type == 1){
    //     foreach($request->approve_all as $key => $val){
    //       $arr = explode("-",$val);
    //         if($request->btn == "true"){
    //           $update = DB::table('export_estimates')
    //             ->where('year', date('Y')+544)
    //             ->where('account', $arr[0])
    //             ->where('div_center', $request->div_id)
    //             ->where('fund_center', $request->fund_id)
    //             ->where('version',1)
    //             ->update(['status' => 1 ,'budget'=> $request->new2[$arr[0]][$arr[1]],'approve_all' => Auth::user()->emp_id ,'updated_at' => Carbon::now()]);
    //
    //         }
    //     }
    //     if($update){
    //       return back()->with('success', 'บันทึกข้อมูลแล้ว');
    //     }
    //   }
    //
    // }
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
     // dd($data->toArray());
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
    // Structure::all()->delete();
    DB::table('structures')->delete();
    foreach($data->toArray() as $value){
      if($value['Company']){
        $insert = new Structure;
        $insert->Company = 1000;
        $insert->Division = $value['Division'];
        $insert->FundsCenterID = $value['FundsCenterID'];
        $insert->FundID = $value['FundID'];
        $insert->CostCenterID = $value['CostCenterID'];
        $insert->CostCenterTitle = $value['CostCenterTitle'];
        $insert->CostCenterName = $value['CostCenterName'];
        $insert->NT = $value['NT'];
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
      $edit->Company = 1000;
      $edit->Division = $request->Division;
      $edit->FundsCenterID = $request->FundsCenterID;
      $edit->CostCenterID = $request->CostCenterID;
      $edit->CostCenterTitle = $request->CostCenterTitle;
      $edit->CostCenterName = $request->CostCenterName;
      $edit->NT = $request->NT;
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
      $add->NT = $request->NT;
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
        // $sap = Estimate::select('stat_year','version','fund_center','account',DB::raw('SUM(budget) as budget'))
        //   ->where('stat_year', $request->stat_year)
        //   ->where('status',1)
        //   ->groupBy('stat_year','version','fund_center','account')
        //   ->get()->toArray();
        $sap = Export_estimate::select('year','version','fund_center','account',DB::raw('SUM(budget) as budget'))
          ->where('year', $request->stat_year)
          ->where('status',1)
          ->groupBy('year','version','fund_center','account')
          ->get()->toArray();
        // dd($sap);
        foreach ($sap as $key => $value) {
              $data[] = array(
                'stat_year' => $value['year']-543,
                'from' => 1,
                'to' => 1,
                'version' => 1,
                'fund' => 'oper',
                'func.a' => 'z0',
                'fund_center' => $value['fund_center'],
                'cmmt' => $value['account'],
                // 'name' => Func::get_account($value['account']),
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

      if(Auth::user()->type == 5 || Auth::user()->type == 6 || Auth::user()->type == 1){
        $get_status = null;
        $cmmt = Cmmt::get();
        $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
        $first = $str->first()->FundsCenterID;
        return view('status_report',['status' => $get_status,'cate' => 1 ,'cmmt' => $cmmt ,'cat_cm' => 1,'str' => $str ,'divid' => $first]);
      }
      if(Auth::user()->type == 4){
        $get_status = Estimate::select('stat_year','cost_title','fund_center','center_money','status',DB::raw('SUM(budget) as budget'))
          ->where('status_ver',1)
          ->where('stat_year',Func::get_year())
          ->where('fund_center',Auth::user()->fund_center)
          ->groupBy('stat_year','fund_center','cost_title','center_money','status')
          ->orderBy('center_money','asc')
          ->get()->toArray();
      }else{
        $get_status = Estimate::select('stat_year','cost_title','fund_center','center_money','status',DB::raw('SUM(budget) as budget'))
          ->where('status_ver',1)
          ->where('stat_year',Func::get_year())
          ->where('center_money',Auth::user()->center_money)
          ->groupBy('stat_year','fund_center','cost_title','center_money','status')
          ->orderBy('center_money','asc')
          ->get()->toArray();
          // dd($get_status);
      }

// dd($get_status);
      return view('status_report',['status' => $get_status,'year' => Func::get_year()]);
    }

    public function post_status(Request $request)
    {
      // วง สามารถค้นหาชื่อฝ่ายได้
      // dd($request->all());
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $get_status = Estimate::select('stat_year','fund_center','cost_title','center_money','status',DB::raw('SUM(budget) as budget'))
        ->where('status_ver',1)
        ->where('stat_year',Func::get_year())
        ->where('div_center',$request->div_id)
        ->groupBy('stat_year','fund_center','cost_title','center_money','status')
        ->orderBy('fund_center','asc')
        ->orderBy('center_money','asc')
        ->get()->toArray();
//     foreach($center as $data){
//       $first = $data->center_money;
//       $last_ver = Func::get_last_version(date('Y')+543,$first);
// // dd($last_ver);
//       $get_status[] = Estimate::select('stat_year','cost_title','center_money','status',DB::raw('SUM(budget) as budget'))
//       ->where('center_money',$first)
//       ->where('stat_year',date('Y')+543)
//       ->where('version',$last_ver)
//       ->groupBy('status','cost_title','center_money','stat_year')
//       ->get()->toArray();
//     }

        // dd($get_status);

      return view('status_report',['status' => $get_status, 'divid' => $request->div_id,'str' => $str]);
    }

    public function get_version()
    {
      // ดูversionงบประมาณ
      $last_ver = Func::get_last_version(Func::get_year(),Auth::user()->center_money);
      // $last_ver  = Estimate::where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->latest()->first();
      // dd($last_ver);
      $data = Estimate::where('version',$last_ver)
      ->where('stat_year', Func::get_year())
      ->where('center_money',Auth::user()->center_money)
      ->get();

      // dd($last_ver->version);
      return view('view_version', ['data' => $data,'versions' => $last_ver,'version' => $last_ver]);
    }
    public function post_version(Request $request)
    {
      // ค้นหางบประมาณversionต่าง
      $last_ver = Func::get_last_version(Func::get_year(),Auth::user()->center_money);

      // $last_ver  = Estimate::where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->latest()->first();
      $data = Estimate::where('version',$request->version)
        ->where('stat_year', Func::get_year())
        ->where('center_money',Auth::user()->center_money)
        ->get();

      return view('view_version', ['data' => $data,'versions' => $last_ver ,'version' => $request->version]);
    }

    public function get_view_estimate()
    {
      // dd(998);
      $cmmt = Cmmt::get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $first = $str->first()->FundsCenterID;
      $fund_cen = Structure::select(DB::raw('DISTINCT CostCenterID,CostCenterName'))->where('FundID',Auth::user()->fund_center)->whereNotNull('CostCenterID')->get();
      // dd($fund_cen);
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        return view('view_estimate',['cmmt' => $cmmt ,'cat_cm' => 1,'str' => $str ,'divid' => $first]);
      }
      else{
        return view('view_estimate',['cmmt' => $cmmt ,'cat_cm' => 1, 'fund_cen' => $fund_cen, 'fun_id' =>$fund_cen->first()]);
      }

      return view('view_estimate',['head' => $head,'name' => $all_name,'now' => $now,'year3' => $year3 ,'year2' => $year2, 'year1' => $year1 ]);

      // return view('view_estimate');
    }
    public function post_view_estimate(Request $request)
    {
      // dd($request->center_id);
      $cmmt = Cmmt::get();
      $fund_cen = Structure::select(DB::raw('DISTINCT CostCenterID,CostCenterName'))->where('FundID',Auth::user()->fund_center)->whereNotNull('CostCenterID')->get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $name = DB::table('masters')
        ->whereNull('deleted_at')
        ->get()->toArray();
      // เปรียบเทียบข้อมูลงบประมาณ
      foreach ($name as $key => $value) {
        $all_name[$value->id1][$value->id2][Func::get_year()][$value->account] = 0;
        $year3[(Func::get_year()-3)][$value->account] = 0;
        $year2[(Func::get_year()-2)][$value->account] = 0;
        $year1[(Func::get_year()-1)][$value->account] = 0;
        $now[Func::get_year()][$value->account] = NULL;
        $reason[Func::get_year()][$value->account] = NULL;
      }
      if($request->center_id == 'all' && Auth::user()->type == 5){
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('fund_center',$request->fund_id)
            ->groupBy('stat_year','id1','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // dd($year1);
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status','!=',6)
          ->where('fund_center',$request->fund_id)
          ->groupBy('stat_year','id1','account')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
          }
      }elseif(Auth::user()->type == 5){
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('center_money',$request->center_id)
            ->groupBy('stat_year','id1','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // dd($year1);
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','reason', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status','!=',6)
          ->where('center_money',$request->center_id)
          ->groupBy('stat_year','id1','account','reason')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
      }else{
        // dd(4234);
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('center_money',$request->center_id)
            ->groupBy('stat_year','id1','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // dd($year1);
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','reason', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('center_money',$request->center_id)
          ->groupBy('stat_year','id1','account','reason')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
      }

          // dd($reason);
          // $list = Cmmt::get();
          foreach($cmmt as $value){
            $head[$value->name_id] = $value->name;
          }


        return view('view_estimate',['type' => $request->center_id, 'name' => $all_name,'now' => $now,'str' => $str,'divid'=> $request->div_id, 'year3' => $year3 ,'year2' => $year2,'reason' =>$reason ,'year1' => $year1 ,'cate' => 1 , 'head' => $head ,'fund_cen' => $fund_cen,'fun_id' => $request->center_id]);

    }

    public function get_view_estimate_export()
    {
      // dd(998);
      $cmmt = Cmmt::get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $first = $str->first()->FundsCenterID;
      $fund_cen = Structure::select(DB::raw('DISTINCT CostCenterID,CostCenterName'))->where('FundID',Auth::user()->fund_center)->whereNotNull('CostCenterID')->get();
      // dd($fund_cen);
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        return view('view_estimate_export',['fund' => null, 'type' => null,'cmmt' => $cmmt ,'cat_cm' => 1,'str' => $str ,'divid' => $first]);
      }else{
        return view('view_estimate_export',['type' => null,'cmmt' => $cmmt ,'cat_cm' => 1, 'fund_cen' => $fund_cen, 'fun_id' =>$fund_cen->first()]);
      }

      return view('view_estimate_export',['type' => null,'head' => $head,'name' => $all_name,'now' => $now,'year3' => $year3 ,'year2' => $year2, 'year1' => $year1 ]);

      // return view('view_estimate');
    }
    public function post_view_estimate_export(Request $request)
    {
      // dd($request->all());
      $cmmt = Cmmt::get();
      $fund_cen = Structure::select(DB::raw('DISTINCT CostCenterID,CostCenterName'))->where('FundID',Auth::user()->fund_center)->whereNotNull('CostCenterID')->get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $name = DB::table('masters')
        ->whereNull('deleted_at')
        ->get()->toArray();
      // เปรียบเทียบข้อมูลงบประมาณ
      foreach ($name as $key => $value) {
        $all_name[$value->id1][$value->id2][Func::get_year()][$value->account] = 0;
        $year3[(Func::get_year()-3)][$value->account] = 0;
        $year2[(Func::get_year()-2)][$value->account] = 0;
        $year1[(Func::get_year()-1)][$value->account] = 0;
        $now[Func::get_year()][$value->account] = NULL;
        $reason[Func::get_year()][$value->account] = NULL;
      }
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $fund = $request->fund_id;
      }else{
        $fund = Auth::user()->fund_center;
      }
      if($request->center_id == 'all'){
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('fund_center',$fund)//
            ->groupBy('stat_year','id1','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('fund_center',$fund)//
          ->groupBy('stat_year','id1','account')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
          }
      }else{
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('center_money',$request->center_id)
            ->groupBy('stat_year','id1','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // dd($year1);
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','reason', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('center_money',$request->center_id)
          ->groupBy('stat_year','id1','account','reason')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $now[$value->stat_year][$value->account] = $value->budget;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
      }
      foreach($cmmt as $value){
        $head[$value->name_id] = $value->name;
      }

        return view('view_estimate_export',['type' => $request->center_id, 'name' => $all_name,'now' => $now,'str' => $str,'divid'=> $request->div_id, 'year3' => $year3 ,'year2' => $year2,'reason' =>$reason ,'year1' => $year1 ,'cate' => 1 , 'head' => $head ,'fund_cen' => $fund_cen,'fun_id' => $request->center_id,'fund' => $fund]);

    }
    public function get_excel(Request $request)
    {
      $cmmt = Cmmt::get();
      foreach($cmmt as $value){
        $head[$value->name_id] = $value->name;
      }
      $name = DB::table('masters')
        ->whereNull('deleted_at')
        ->get()->toArray();
      // เปรียบเทียบข้อมูลงบประมาณ
      foreach ($name as $key => $value) {
        $all_name[$value->id1][$value->id2][Func::get_year()][$value->account] = 0;
        $year3[(Func::get_year()-3)][$value->account] = 0;
        $year2[(Func::get_year()-2)][$value->account] = 0;
        $year1[(Func::get_year()-1)][$value->account] = 0;
        $now[Func::get_year()][$value->account] = 0;
        $reason[Func::get_year()][$value->account] = NULL;
        $acname[$value->account] = $value->name;
      }
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $fund = $request->fundcenter;
      }else{
        $fund = Auth::user()->fund_center;
      }
      if($request->center == 'all'){
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','id2','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('fund_center', $fund)//
            ->groupBy('stat_year','id1','id2','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              $id_1[$value->id1][Func::get_year()] = $value->id1;
              $id_2[$value->id1][$value->id2] = $value->id2;
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','id2', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('fund_center',$fund)//
          ->groupBy('stat_year','id1','id2','account')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $id_1[$value->id1][Func::get_year()] = $value->id1;
            $id_2[$value->id1][$value->id2] = $value->id2;
            $now[$value->stat_year][$value->account] = $value->budget;
          }
          // $datas[] = array(
          //   'account' => 'งบประมาณทำการประจำปี'.(date('Y')+544)
          // );
          // $datas[] = array(
          //   'account' => Func::get_office_name($fund)
          // );
          // $datas[]  = array('account' => 'รายการ' ,'y3' => 'ประมาณจ่ายจริงปี '.(date('Y')+541),'y2' => 'ประมาณจ่ายจริงปี '.(date('Y')+542),'y1' => 'ประมาณจ่ายจริงปี '.(date('Y')+543),'new'=>'งบประมาณของตั้งปี '.(date('Y')+544));
          //
          // $all3 = 0;
          // $all2 = 0;
          // $all1 = 0;
          // $all = 0;
          // foreach($all_name as $id1 => $arr_id2){
          //   $sum3 = 0;
          //   $sum2 = 0;
          //   $sum1 = 0;
          //   $sum = 0;
          //   if(isset($id_1[$id1][date("Y")+544])){
          //     if($id_1[$id1][date("Y")+544] == $id1){
          //       $datas[] = array(
          //         'account' => $head[$id1]
          //       );
          //     }
          //   }
          //
          //   foreach($arr_id2 as $id2 => $arr_year){
          //     if(isset($id_1[$id1][date("Y")+544]) && isset($id_2[$id1][$id2])){
          //       if($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 1){
          //         $id = '1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน';
          //         $datas[] = array(
          //           'account' => $id
          //         );
          //       }elseif($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 2){
          //         $id ='1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร';
          //         $datas[] = array(
          //           'account' => $id
          //         );
          //       }
          //       if($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 1){
          //         $id ='2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง';
          //         $datas[] = array(
          //           'account' => $id
          //         );
          //       }elseif($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 2){
          //         $id ='2.2 ค่าสวัสดิการผู้บริหาร';
          //         $datas[] = array(
          //           'account' => $id
          //         );
          //       }
          //     }
          //     foreach($arr_year as $year =>$arr_acc){
          //       foreach($arr_acc as $account => $value){
          //         if($year3[date("Y")+541][$account] > 0 || $year2[date("Y")+542][$account] > 0 || $year1[date("Y")+543][$account] > 0 || $now[$year][$account] > 0){
          //           $sum3 += $year3[date("Y")+541][$account];
          //           $sum2 += $year2[date("Y")+542][$account];
          //           $sum1 += $year1[date("Y")+543][$account];
          //           $sum += $now[$year][$account];
          //           $datas[] = array(
          //             'account' => $account.' '.Func::get_account($account),
          //             'y3' => $year3[date("Y")+541][$account],
          //             'y2' => $year2[date("Y")+542][$account],
          //             'y1' => $year1[date("Y")+543][$account],
          //             'now' => $now[$year][$account]
          //           );
          //         }
          //       }
          //     }
          //   }
          //   if(isset($id_1[$id1][date("Y")+544]) && $id_1[$id1][date("Y")+544] == $id1){
          //     $datas[] = array(
          //       'account' => 'Sum',
          //       'y3' => $sum3,
          //       'y2' => $sum2,
          //       'y1' => $sum1,
          //       'now' => $sum
          //     );
          //     $all3 += $sum3;
          //     $all2 += $sum2;
          //     $all1 += $sum1;
          //     $all += $sum;
          //   }
          // }
            $count = 5;
      }else{
        for($i = date("Y",strtotime("-3 year"))+544 ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','id2','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',2)
            ->where('center_money',$request->center)
            ->groupBy('stat_year','id1','id2','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              $id_1[$value->id1][Func::get_year()] = $value->id1;
              $id_2[$value->id1][$value->id2] = $value->id2;
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','id2','reason', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('center_money',$request->center)
          ->groupBy('stat_year','id1','id2','account','reason')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $id_1[$value->id1][Func::get_year()] = $value->id1;
            $id_2[$value->id1][$value->id2] = $value->id2;
            $now[$value->stat_year][$value->account] = $value->budget;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
            $count = 6;
        }
          // dd($now);
      //     $datas[] = array(
      //       'account' => 'งบประมาณทำการประจำปี'.(date('Y')+544)
      //     );
      //     $datas[] = array(
      //       'account' => Func::get_name_costcenter($request->center)
      //     );
      //     $datas[]  = array('account' => 'รายการ' ,'y3' => 'ประมาณจ่ายจริงปี '.(date('Y')+541),'y2' => 'ประมาณจ่ายจริงปี '.(date('Y')+542),'y1' => 'ประมาณจ่ายจริงปี '.(date('Y')+543),'new'=>'งบประมาณของตั้งปี '.(date('Y')+544) ,'reason' => 'คำอธิบาย');
      //
      //     $all3 = 0;
      //     $all2 = 0;
      //     $all1 = 0;
      //     $all = 0;
      //     foreach($all_name as $id1 => $arr_id2){
      //       $sum3 = 0;
      //       $sum2 = 0;
      //       $sum1 = 0;
      //       $sum = 0;
      //       if(isset($id_1[$id1][date("Y")+544])){
      //         if($id_1[$id1][date("Y")+544] == $id1){
      //           $datas[] = array(
      //             'account' => $head[$id1]
      //           );
      //         }
      //       }
      //
      //       foreach($arr_id2 as $id2 => $arr_year){
      //         if(isset($id_1[$id1][date("Y")+544]) && isset($id_2[$id1][$id2])){
      //           if($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 1){
      //             $id = '1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน';
      //             $datas[] = array(
      //               'account' => $id
      //             );
      //           }elseif($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 2){
      //             $id ='1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร';
      //             $datas[] = array(
      //               'account' => $id
      //             );
      //           }
      //           if($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 1){
      //             $id ='2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง';
      //             $datas[] = array(
      //               'account' => $id
      //             );
      //           }elseif($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 2){
      //             $id ='2.2 ค่าสวัสดิการผู้บริหาร';
      //             $datas[] = array(
      //               'account' => $id
      //             );
      //           }
      //         }
      //         foreach($arr_year as $year =>$arr_acc){
      //           foreach($arr_acc as $account => $value){
      //             if($year3[date("Y")+541][$account] > 0 || $year2[date("Y")+542][$account] > 0 || $year1[date("Y")+543][$account] > 0 || $now[$year][$account] > 0){
      //               $sum3 += $year3[date("Y")+541][$account];
      //               $sum2 += $year2[date("Y")+542][$account];
      //               $sum1 += $year1[date("Y")+543][$account];
      //               $sum += $now[$year][$account];
      //               $datas[] = array(
      //                 'account' => $account.' '.Func::get_account($account),
      //                 'y3' => $year3[date("Y")+541][$account],
      //                 'y2' => $year2[date("Y")+542][$account],
      //                 'y1' => $year1[date("Y")+543][$account],
      //                 'now' => $now[$year][$account],
      //                 'reason' => $reason[date("Y")+544][$account]
      //               );
      //             }
      //           }
      //         }
      //       }
      //       if(isset($id_1[$id1][date("Y")+544]) && $id_1[$id1][date("Y")+544] == $id1){
      //         $datas[] = array(
      //           'account' => 'Sum',
      //           'y3' => $sum3,
      //           'y2' => $sum2,
      //           'y1' => $sum1,
      //           'now' => $sum
      //         );
      //         $all3 += $sum3;
      //         $all2 += $sum2;
      //         $all1 += $sum1;
      //         $all += $sum;
      //       }
      //     }
      // }
      // // dd($id_2);
      //
      // $datas[] = array(
      //   'account' => 'Sum',
      //   'y3' => $all3,
      //   'y2' => $all2,
      //   'y1' => $all1,
      //   'now' => $all
      // );

      $type = $request->center;
// dd($datas);
      Excel::create('Pre Approve_'.$request->center, function($excel) use ($acname, $count,$head,$all_name,$year3,$year2,$year1,$now,$reason,$id_1,$id_2,$type,$fund) {
      $excel->sheet('Excel sheet', function($sheet) use ($acname, $count,$head,$all_name,$year3,$year2,$year1,$now,$reason,$id_1,$id_2,$type,$fund) {
         $sheet->loadView('excel')->with('acname',$acname)
                               ->with('count',$count)
                               ->with('head',$head)
                               ->with('all_name',$all_name)
                               ->with('year3',$year3)
                               ->with('year2',$year2)
                               ->with('year1',$year1)
                               ->with('now',$now)
                               ->with('reason',$reason)
                               ->with('id_1',$id_1)
                               ->with('id_2',$id_2)
                               ->with('type',$type)
                               ->with('fund', $fund);
          $sheet->setWidth('F', 10);
         // $sheet->setOrientation('landscape');
           });

       })->export('xlsx');
      // Excel::create('Pre Approve_'.$request->center,function($excel) use ($datas){
      //   $excel->setTitle('Estimate');
      //   $excel->sheet('Estimate',function($sheet) use ($datas){
      //     $sheet->fromArray($datas,null,'A1',false,false);
      //   });
      // })->download('xlsx');
    }
    public function get_pdf(Request $request)
    {
      $cmmt = Cmmt::get();
      foreach($cmmt as $value){
        $head[$value->name_id] = $value->name;
      }
      $name = DB::table('masters')
        ->whereNull('deleted_at')
        ->get()->toArray();
      // เปรียบเทียบข้อมูลงบประมาณ
      foreach ($name as $key => $value) {
        $all_name[$value->id1][$value->id2][Func::get_year()][$value->account] = 0;
        $year3[(Func::get_year()-3)][$value->account] = 0;
        $year2[(Func::get_year()-2)][$value->account] = 0;
        $year1[(Func::get_year()-1)][$value->account] = 0;
        $now[Func::get_year()][$value->account] = 0;
        $reason[Func::get_year()][$value->account] = NULL;
        $acname[$value->account] = $value->name;
      }
      if(Auth::user()->type == 5 || Auth::user()->type == 1){
        $fund = $request->fundcenter;
      }else{
        $fund = Auth::user()->fund_center;
      }
      if($request->center == 'all'){
        for($i = (Func::get_year()-3) ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','id2','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',1)
            ->where('fund_center', $fund)//
            ->groupBy('stat_year','id1','id2','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              $id_1[$value->id1][Func::get_year()] = $value->id1;
              $id_2[$value->id1][$value->id2] = $value->id2;
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','id2', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('fund_center',$fund)//
          ->groupBy('stat_year','id1','id2','account')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $id_1[$value->id1][Func::get_year()] = $value->id1;
            $id_2[$value->id1][$value->id2] = $value->id2;
            $now[$value->stat_year][$value->account] = $value->budget;
          }
          $count = 5;
      }else{
        for($i = (Func::get_year()-3) ; $i <= (Func::get_year()-1) ; $i++){
          // $last_ver = Func::get_last_version($i ,Auth::user()->center_money);
          $list = DB::table('estimates')
            ->select('stat_year','id1','id2','account', DB::raw('SUM(budget) as budget'))
            ->where('stat_year',$i)
            ->whereNull('deleted_at')
            ->where('status_ver', 1)
            ->where('status',1)
            ->where('center_money',$request->center)
            ->groupBy('stat_year','id1','id2','account')
            ->orderBy('id1','DESC')
            ->get()->toArray();
            foreach ($list as $key => $value) {
              $id_1[$value->id1][Func::get_year()] = $value->id1;
              $id_2[$value->id1][$value->id2] = $value->id2;
              if ($value->stat_year == (Func::get_year()-3)) {
                $year3[$value->stat_year][$value->account] = $value->budget;
              }if ($value->stat_year == (Func::get_year()-2)) {
                $year2[$value->stat_year][$value->account] = $value->budget;
              }if($value->stat_year == (Func::get_year()-1)){
                $year1[$value->stat_year][$value->account] = $value->budget;
              }
            }
        }
        // query ข้อมูลงบปีปัจจุบัน
        $list_now = DB::table('estimates')
          ->select('stat_year','id1','account','id2','reason', DB::raw('SUM(budget) as budget'))
          ->where('stat_year',Func::get_year())
          ->whereNull('deleted_at')
          ->where('status_ver', 1)
          ->where('status',0)
          ->where('center_money',$request->center)
          ->groupBy('stat_year','id1','id2','account','reason')
          ->orderBy('id1','DESC')
          ->get()->toArray();
          foreach ($list_now as $key => $value) {
            $id_1[$value->id1][Func::get_year()] = $value->id1;
            $id_2[$value->id1][$value->id2] = $value->id2;
            $now[$value->stat_year][$value->account] = $value->budget;
            $reason[$value->stat_year][$value->account] = $value->reason;
          }
          $count = 6;
      }
      $type = $request->center;
      // dd($list_now);
              // return view('pdf',['fund'=>$fund,'acname'=> $acname,'count' => $count,'head'=> $head,'all_name' => $all_name,'year3'=>$year3,'year2'=>$year2,'year1'=>$year1,'now'=>$now,'reason'=> $reason, 'id_1'=>$id_1,'id_2'=>$id_2,'type'=>$request->center]);
        $pdf = PDF::loadView('pdf',['fund'=>$fund,'acname'=> $acname,'count' => $count,'head'=> $head,'all_name' => $all_name,'year3'=>$year3,'year2'=>$year2,'year1'=>$year1,'now'=>$now,'reason'=> $reason, 'id_1'=>$id_1,'id_2'=>$id_2,'type'=>$request->center]);
        // $pdf = Excel::loadView('pdf',['acname'=> $acname,'count' => $count,'head'=> $head,'all_name' => $all_name,'year3'=>$year3,'year2'=>$year2,'year1'=>$year1,'now'=>$now,'reason'=> $reason, 'id_1'=>$id_1,'id_2'=>$id_2,'type'=>$request->center]);

        // $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Pre Approve_'.$request->center.'.pdf');
    }
    public function print_all(Request $request)
    {
      // export file ข้อมูลการของบประมาณ(หน้า report_apv)
      // dd($request->all());
      if(Auth::user()->type == 4){
        $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',$request->year)
          ->where('status_ver',1)
          ->where('status','!=',6)
          ->where('fund_center',Auth::user()->fund_center)
          ->where('center_money','like','%'.$request->center_money.'%')
          ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->orderBy('center_money')
          ->get()->toArray();
      }elseif(Auth::user()->type == 2 || Auth::user()->type == 3){
        $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',$request->year)
          ->where('status_ver',1)
          ->where('status','!=',6)
          ->where('center_money',Auth::user()->center_money)
          ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->orderBy('center_money')
          ->get()->toArray();
      }else{
        $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
          ->where('stat_year',$request->year)
          ->where('status_ver',1)
          ->where('status','!=',6)
          ->where('fund_center',$request->cost_title)
          ->where('center_money','like','%'.$request->center_money.'%')
          ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
          ->orderBy('center_money')
          ->get()->toArray();
      }

        $datas[]  = array('year' => 'ปีงบประมาณ' ,'center' => 'ศูนย์ต้นทุน','account' => 'หมวดค่าใช้จ่าย','name' => 'รายการภาระผูกพัน','amount' => 'งบประมาณ' ,'status' => 'สถานะ');
// dd($datas);
      foreach ($view as $key => $value) {
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
      // $center_money = Estimate::select('center_money')->where('stat_year',date('Y')+543)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
      $view = null;
      $fund = Structure::select(DB::raw('DISTINCT FundID,CostCenterName'))->whereNotNull('FundID')->whereNull('CostCenterID')->get();
      $fund_id = $fund->first();
      // dd($fund_id->FundID);



      return view('report_apv',['fund_id'=>$fund_id->FundID,'fund' => $fund,'views' => $view ,'yy' => Func::get_year(), 'centermoney' => null,'fund_id' => null]);

    }
    public function post_report_apv(Request $request)
    {
      // dd($request->all());
      // หน้า report apv

      $fund = Structure::select(DB::raw('DISTINCT FundID,CostCenterName'))->whereNotNull('FundID')->whereNull('CostCenterID')->get();
      $view = NULL;
          // $last_ver = Func::get_last_version($request->stat_year,$data->center_money);
          if(Auth::user()->type == "2" || Auth::user()->type == "3"){
            $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
              ->where('stat_year',$request->stat_year)
              ->where('status_ver',1)
              ->where('status','!=',6)
              ->where('center_money',Auth::user()->center_money)
              ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
              ->orderBy('center_money')
              ->get()->toArray();
          }elseif(Auth::user()->type == "4"){
            $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
              ->where('stat_year',$request->stat_year)
              ->where('status_ver',1)
              ->where('status','!=',6)
              ->where('fund_center',Auth::user()->fund_center)
              ->where('center_money','like','%'.$request->center_money.'%')
              ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
              ->orderBy('center_money')
              ->get()->toArray();
          }else{
            $view = Estimate::select(DB::raw('status,version, center_money,fund_center,cost_title,stat_year,account,approve_by1,approve_by2,sum(budget) as budget'))
              ->where('stat_year',$request->stat_year)
              ->where('status_ver',1)
              ->where('status','!=',6)
              ->where('fund_center',$request->fund_id)
              ->where('center_money','like','%'.$request->center_money.'%')
              ->groupBy('status', 'center_money','version','stat_year','fund_center','cost_title','account','approve_by1','approve_by2')
              ->orderBy('center_money')
              ->get()->toArray();
          }



      // dd($view);

      return view('report_apv',['fund' => $fund,'views' => $view ,'yy' => $request->stat_year, 'centermoney' => $request->center_money,'fund_id' =>$request->fund_id]);

    }

    public function get_compare()
    {
      $cmmt = Cmmt::get();
      $id1 = $cmmt->first();
      $fund_id = null;
      if(Auth::user()->type == 5 ||Auth::user()->type == 6 || Auth::user()->type == 1){
        $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
        $divid = $str->first();
        return view('report_compare',['fund_id' => $fund_id,'id1' => $id1 ,'cmmt' => $cmmt ,'divid' => $divid,'str' => $str,'yy' => Func::get_year(), 'fund' => NULL ,'center' => NULL ,'account' => NULL]);

      }

      return view('report_compare',['fund_id' => $fund_id,'id1' => $id1 ,'cmmt' => $cmmt ,'yy' => Func::get_year(), 'fund' => NULL ,'center' => NULL ,'account' => NULL]);
    }

    public function post_compare(Request $request)
    {
      // dd($request->all());
      // หน้า report compare
      $cmmt = Cmmt::get();
      $str = Structure::select('FundsCenterID')->groupBy('FundsCenterID')->get();
      $name = DB::table('masters')
        ->where('id1',$request->id1)
        ->whereNull('deleted_at')
        ->get()->toArray();
         foreach ($name as $key => $value) {
           $data_old[$value->account][date("Y",strtotime("-1 year"))+544] = 0;
           $data[$value->account][Func::get_year()] = 0;
         }
         $view =[];
         $old =[];
      if(Auth::user()->type == "5" ||Auth::user()->type == 6 || Auth::user()->type == "1"){
          $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('id1',$request->id1)
            ->where('fund_center',$request->fund_id)
            ->where('stat_year',$request->stat_year)
            ->where('status','!=',6)
            ->where('status_ver',1)
            ->groupBy('account','stat_year')->get();
  // dd($last_ver);
          $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('id1',$request->id1)
            ->where('fund_center',$request->fund_id)
            ->where('stat_year',($request->stat_year-1))
            ->where('status',1)
            ->where('status_ver',2)
            ->groupBy('account','stat_year')->get();
            $div_id = $request->div_id;
            $fund_id = $request->fund_id;
            // $center = $id->CostCenterID;

      }else{
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('id1',$request->id1)
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',$request->stat_year)
          ->where('status','!=',6)
          ->where('status_ver',1)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('id1',$request->id1)
          ->where('center_money',Auth::user()->center_money)
          ->where('stat_year',($request->stat_year-1))
          ->where('status_ver',1)
          ->where('status',2)
          ->groupBy('account','stat_year')->get();
          $div_id = null;
          $fund_id = Auth::user()->center_money;
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

      $datas = array_merge_recursive($data_old,$data);
      // dd($datas);

      // return Redirect::route('post_compare',array('data' => $datas,'data_old' => $data_old,'account'=>$request->account ,'fund'=> $fund,'yy' => $request->stat_year, 'center' => $center))->withInput();
// session()->flashInput($request->all());
      return view('report_compare',['cmmt' => $cmmt,'fund_id'=> $fund_id ,'data' => $datas,'id1' => $request->id1,'str' => $str ,'data_old' => $data_old,'account'=>$request->account ,'divid'=> $div_id,'yy' => $request->stat_year]);

    }

    public function print_compare(Request $request)
    {
      // export file compare
      // dd($request->all());
      $name = DB::table('masters')
        ->where('id1',$request->account)
        ->whereNull('deleted_at')
        ->whereNull('deleted_at')
        ->get();
         foreach ($name as $key => $value) {
           $data_old[$value->account][date("Y",strtotime("-1 year"))+544] = 0;
           $data[$value->account][Func::get_year()] = 0;
         }
       $fund = NULL;
       $center = NULL;
       $view =[];
       $old =[];
      if(Auth::user()->type == "5" || Auth::user()->type == 6 ||Auth::user()->type == "1"){
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('id1',$request->account)
          ->where('fund_center',$request->fundcenter)
          ->where('stat_year',$request->statyear)
          ->where('status','!=',6)
          ->where('status_ver',1)
          ->groupBy('account','stat_year')->get();

        $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
        ->where('id1',$request->account)
        ->where('fund_center',$request->fundcenter)
        ->where('stat_year',($request->statyear-1))
        ->where('status',2)
        ->where('status_ver',1)
        ->groupBy('account','stat_year')->get();
      }else{
        // dd($last_ver);
        $view = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
          ->where('id1',$request->account)
          ->where('center_money',$request->fundcenter)
          ->where('stat_year',$request->statyear)
          ->where('status','!=',6)
          ->where('status_ver',1)
          ->groupBy('account','stat_year')->get();

          $old = Estimate::select('account','stat_year',DB::raw('SUM(budget) as budget'))
            ->where('id1',$request->account)
            ->where('center_money',$request->fundcenter)
            ->where('stat_year',($request->statyear-1))
            ->where('status_ver',1)
            ->where('status',2)
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
      $data = array_merge_recursive($data_old,$data);
      $datas[]  = array('account' => 'บัญชีรายการภาระผูกพัน' ,'name' => 'รายการภาระผูกพัน','fund' => 'ฝ่าย/ส่วน','before' => 'ปีงบประมาณ '.($request->statyear-1) ,'now' => 'ปีงบประมาณ '.$request->statyear);
// dd($data);
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
        if(Auth::user()->type == 2 || Auth::user()->type == 3){
          $fund = Func::get_name_costcenter($request->fundcenter);
        }else{
          $fund = Func::FundID_name($request->fundcenter);
        }
          $datas[] = array(
            'account' => $key,
            'name' => Func::get_account($key),
            'fund' => $fund,
            'before' => $before,
            'now' => $now
          );
      }
      Excel::create('View Estimate Compare'.$request->statyear,function($excel) use ($datas){
        $excel->setTitle('Compare');
        $excel->sheet('Compare',function($sheet) use ($datas){
          $sheet->fromArray($datas,null,'A1',false,false);
        });
      })->download('xlsx');
    }
}
