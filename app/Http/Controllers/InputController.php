<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\FileImport;
use App\Exports\FileExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Branch;
use App\Transaction;
use App\Estimate;
use App\Information;
use App\Budget;
use App\User_request;
use App\Event;
use App\Export_estimate;
use DB;
use Excel;
use Func;
use Calendar;

class InputController extends Controller
{
    public function get_source()
    {
      // $group_status = [];
      $data = Information::orderBy('id','DESC')->get();
      if(Auth::user()->type == 4){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+544)->where('fund_center',Auth::user()->fund_center)->groupBy('center_money')->get();
        // dd($center->count());
        for($i=0 ;$i< $center->count() ; $i++){
          $last = Func::get_last_version(date('Y')+544,$center[$i]->center_money);
          $group_status[] = Estimate::select('status',DB::raw('SUM(budget) as budget'))->where('center_money',$center[$i]->center_money)->where('fund_center',Auth::user()->fund_center)->where('version',$last)->where('stat_year',date('Y')+544)->groupBy('status')->get();

        }
        // dd($group_status);
        $stat= array('6'=> 0,'5'=> 0,'0'=> 0, '1'=>0,'2'=>0,'3'=>0,'4'=>0);
        if(isset($group_status)){
          foreach ($group_status as $key => $arr_val) {
            foreach($arr_val as $key2 => $val){
              // dd($val);
              $stat[$val->status] += $val->budget;
            }
          }
        }

  // dd($stat);
        return view('dashboard',['stat'=>$stat ,'data' => $data]);
      }
// dd(count($stat));
      if(Auth::user()->type == 2 ||Auth::user()->type == 3){
        $center = Estimate::select('center_money')->where('stat_year',date('Y')+544)->where('center_money',Auth::user()->center_money)->groupBy('center_money')->get();
        $last_version = Func::get_last_version(date('Y')+544,Auth::user()->center_money);
        // dd($last_version);
        $group_status = Estimate::select('status',DB::raw('SUM(budget) as budget'))->where('version',$last_version)->where('stat_year',date('Y')+544)->where('center_money',Auth::user()->center_money)->groupBy('status')->get();
        $stat= array('5'=> 0,'0'=> 0, '1'=>0,'2'=>0, '3'=>0,'4'=>0);
        // dd($group_status);

        foreach ($group_status as $key) {
          $stat[$key->status] = $key->budget;
        }

      // dd($center);
      if($center->count()){
        // dd(9999);
        $first = $center->first();
        // dd($first->center_money);
        $firstcen = $first->center_money;
        $last_ver = Func::get_last_version(date('Y')+544,$firstcen);
  // dd($last_ver);
        $get_status = Estimate::select('stat_year','center_money','status',DB::raw('SUM(budget) as budget'))
        ->where('center_money',$firstcen)
        ->where('stat_year',date('Y')+544)
        ->where('version',$last_ver)
        ->groupBy('status','center_money','stat_year')
        ->get()->toArray();
  // dd($get_status);
      }else{
        $firstcen = NULL;
        $center = NULL;
        $get_status = NULL;
      }
      return view('dashboard',['stat'=>$stat ,'data' => $data,'status' => $get_status,'year' => date('Y')+544,'center' => $center,'first'=> $firstcen]);

    }
// dd($status);
    return view('dashboard',['data' => $data]);

      // return view('status_report',['status' => $get_status,'year' => date('Y')+543,'center' => $center,'first'=> $firstcen]);
    }
    public function delete_infor(Request $request)
    {
      // dd();
      $del = Information::find($request->id);
      $del->delete();

      if($del){
        return back()->with('success', 'ลบประกาศแล้ว');
      }

    }

    public function open( $filename = '' )
    {

         // Check if file exists in app/storage/file folder
         $file_path = storage_path() . "/log_info/" . $filename;
         // dd($file_path);
         $headers = array(
             'Content-Type: application/pdf',
             'Content-Disposition: attachment; filename='.$filename,
         );
         // dd($headers);
         if ( file_exists( $file_path ) ) {
             // Send Download
             return \Response::download( $file_path, $filename, $headers );
         } else {
             // Error
             exit( 'Requested file does not exist on our server!' );
         }
    }

    public function post_information(Request $request)
    {
      // dd($request->all());
      $this->validate($request, [
        'name' => 'required',
        'select_file'  => 'required'
      ]);
      // dd($request->file('select_file'));

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('info_log')->getAdapter()->getPathPrefix();
     Storage::disk('info_log')->put($name, File::get($request->file('select_file')));


     $add = new Information;
     $add->name = $request->name;
     $add->path_file = $name;
     $add->save();

     if($add){
       return back()->with('success', 'เพิ่มประกาศแล้ว');
     }

    }

    public function get_data()
    {

      return view('home');

    }
    public function post_data(Request $request)
    {

      $start_date = $request->start_date;
      $end_date = $request->end_date;
      $source = $request->source;
      $branch = $request->branch;

      $this->validate($request,[
         'start_date'=>'required|date',
         'end_date'=>'required|date',
         'source' => 'required',
         'branch' => 'required|numeric'
      ]);


      $people = new Transaction;
      $people->branch_id = $branch;
      $people->source = $source;
      $people->start_date = $start_date;
      $people->end_date = $end_date;
      $people->user_id = Auth::user()->emp_id;
      $save = $people->save();

      return view('home');
    }
    public function ajax_data()
    {
      $branch = $_POST['data'];
      $data = Branch::where('branch_id',$branch)->first();
      if(!empty($data)){
        return response()->json(['success' => $data->branch_name]);
      }else{
        return response()->json(['success' => 'ไม่มีสาขาที่กรอก']);
      }

    }

    public function get_calendar()
    {
      $events = Event::where('user_id',Auth::user()->emp_id)->orwhere('role',5)->get();
      $event_list = [];
      foreach($events as $key => $event){
        $event_list[] = Calendar::event(
          $event->event_name,
          true,
          new \DateTime($event->start_date),
          new \DateTime($event->end_date.' +1 day')
        );
      }

      $calendar_details = Calendar::addEvents($event_list);

      return view('calendar',compact('calendar_details'));
    }

    public function get_manage()
    {
      $calendar = Event::where('user_id',Auth::user()->emp_id)->get();
      return view('manage_calendar',['calendar' => $calendar]);
    }

    public function post_calendar(Request $request)
    {
      // dd(4234);
      $event_name = $request->event_name;
      $start_date = date('Y-m-d',strtotime($request->start_date));
      $end_date = date('Y-m-d',strtotime($request->end_date));

      $this->validate($request,[
        'event_name' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date'
     ]);

      $event = new Event;
      $event->event_name = $event_name;
      $event->start_date = $start_date;
      $event->end_date = $end_date;
      $event->user_id = Auth::user()->emp_id;
      $event->role = Auth::user()->type;
      $event->save();

      if($event){
        return back()->with('success', 'เพิ่มกิจกรรมแล้ว');
      }
      // return back()->with('success', 'เพิ่มกิจกรรมแล้ว');
    }

    public function post_edit_calendar(Request $request)
    {
      $event_id = $request->id;
      $start_day = date('Y-m-d',strtotime($request->start_day));
      $end_day = date('Y-m-d',strtotime($request->end_day));
      $new_event = $request->new_event;
      // dd($event_id);

      $this->validate($request,[
        'new_event' => 'required',
        'start_day' => 'required|date',
        'end_day' => 'required|date'
     ]);

      $update = Event::find($event_id);
      $update->event_name = $new_event;
      $update->start_date = $start_day;
      $update->end_date = $end_day;
      $update->user_id = Auth::user()->emp_id;
      $update->role = Auth::user()->type;
      $update->update();

      if($update){
        return back()->with('success', 'แก้ไขกิจกรรมแล้ว');
      }
      // return back()->with('success', 'แก้ไขกิจกรรมแล้ว');

    }

    public function post_delete_calendar(Request $request)
    {
      $delete = Event::find($request->id);
      $delete->delete();

      if($delete){
        return back()->with('success', 'ลบกิจกรรมแล้ว');
      }
      // return back()->with('success', 'ลบกิจกรรมแล้ว');
    }

    public function download( $filename = '' )
    {
         // Check if file exists in app/storage/file folder
         $file_path = storage_path() . "/files/" . $filename;
         // dd($file_path);
         $headers = array(
             'Content-Type: application/vnd.ms-excel',
             'Content-Disposition: attachment; filename='.$filename,
         );
         // dd($headers);
         if ( file_exists( $file_path ) ) {
             // Send Download
             return \Response::download( $file_path, $filename, $headers );
         } else {
             // Error
             exit( 'Requested file does not exist on our server!' );
         }
    }

}
