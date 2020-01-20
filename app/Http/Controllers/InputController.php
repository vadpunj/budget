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
use App\User_request;
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


    public function get_calendar()
    {
      $events = Event::get();
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

    public function data_budget()
    {

      $number = count($_POST["list"]);
      // return response()->json(['success' => $_POST["stat_year"]]);
      if($number > 0)
      {
        $data = new User_request;
        $data->year = $_POST["stat_year"];
        $data->field = Auth::user()->field;
        $data->office = Auth::user()->office;
        $data->part = $_POST["part"];
        $data->name = $_POST["name_reqs"];
        $data->phone = $_POST["phone"];
        $data->save();
        // return response()->json(['success' => $data->id]);
        for($i=0; $i<$number; $i++)
        {
          // DB::table('tbl_name')->insert(
          //     ['name' => $_POST["name"][$i]]
          // );
          $insert = new Budget;
          $insert->list = trim($_POST["list"][$i]);
          $insert->business = trim($_POST["business"][$i]);
          $insert->dis_business = trim($_POST["dis_business"][$i]);
          $insert->project = trim($_POST["project"][$i]);
          $insert->activ = trim($_POST["activ"][$i]);
          $insert->respons = trim($_POST["respons"][$i]);
          $insert->amount = trim($_POST["amount"][$i]);
          $insert->price_per = trim($_POST["price_per"][$i]);
          $insert->unit = trim($_POST["unit"][$i]);
          $insert->unitsap = trim($_POST["unitsap"][$i]);
          $insert->total = trim($_POST["total"][$i]);
          $insert->explan = trim($_POST["explan"][$i]);
          $insert->unit_t = trim($_POST["unit_t"][$i]);
          $insert->year = trim($_POST["year"][$i]);
          $insert->status = trim($_POST["status"][$i]);
          $insert->field =  Auth::user()->field;
          $insert->office = Auth::user()->office;
          $insert->user_request_id = $data->id;
          $insert->save();

          // return response()->json(['success' => $_POST["list"][$i]]);
        }
            return response()->json(['success' => 'บันทึกสำเร็จ']);
      }
    }
}
