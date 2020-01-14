<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\FileImport;
use App\Exports\FileExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Branch;
use App\Transaction;
use App\Budget;
use App\Event;
use DB;
use Excel;
use Func;
use Calendar;

class InputController extends Controller
{
    public function get_source()
    {

      return view('dashboard');

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

    public function get_add()
    {
      return view('add');
    }

    public function post_add(Request $request)
    {
      $year = $request->year;
      $list = $request->list;
      $detail = $request->detail;
      $money = $request->money;
      $branch = $request->branch;
      $remark = $request->remark;

      $this->validate($request,[
         'year' => 'required|numeric',
         'money' => 'required|numeric',
         'branch' => 'required|numeric'
      ]);

      $insert = new Budget;
      $insert->list = $list;
      $insert->detail = $detail;
      $insert->money = $money;
      $insert->year = $year;
      $insert->branch = $branch;
      $insert->remark = $remark;

      return view('add');
    }

    public function get_calendar()
    {
      $events = Event::get();
      // $event_list = [];
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

    public function post_calendar(Request $request)
    {
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
      $event->save();
      
      return Redirect::to('/event');
    }
}
