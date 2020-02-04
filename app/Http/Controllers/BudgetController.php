<?php

namespace App\Http\Controllers;

use App\Imports\FileImport;
use App\Exports\FileExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\User_request;
use App\Log_user;
use App\Budget;
use DB;
use Excel;
use Func;
use Response;

class BudgetController extends Controller
{
  public function get_add()
  {
    $user_req = User_request::where('field',Auth::user()->field)->where('type','งบลงทุน')->get();
    return view('add_bud',['user_req' => $user_req]);
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
      $data->type = 'งบลงทุน';
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


  public function post_edit($num)
  {
    // dd($num);
      $user_req = User_request::where('id',$num)->get();
      $budget = Budget::where('user_request_id',$num)->get();
      return view('edit',['user_req' => $user_req,'budget' => $budget]);
  }

  public function post_edit_data()
  {
    if($number > 0)
    {
      $data = User_request::find($_POST["id"]);
      $data->year = $_POST["stat_year"];
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = $_POST["part"];
      $data->name = $_POST["name_reqs"];
      $data->phone = $_POST["phone"];
      $data->type = 'งบลงทุน';
      $data->update();
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

  public function import_index_budget()
  {

    $data = Budget::where('year',date('Y'))->get()->toArray();
    // $data = DB::table('budgets')
    //       ->select(DB::raw('sum(money) as money,business_process,product,functional_area,segment'))
    //       ->whereBetween('date', date('Y'))
    //       ->groupBy('business_process','product','functional_area','segment')
    //       ->get()
    //       ->toArray();
    return view('import_bud',['data' => $data]);
  }

  public function import_budget(Request $request)
  {
    set_time_limit(0);
    $this->validate($request, [
      'select_file'  => 'required|mimes:xlsx'
    ]);

   $path = $request->file('select_file')->getRealPath();
   $name = $request->file('select_file')->getClientOriginalName();
   // $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
   Storage::disk('log')->put($name, File::get($request->file('select_file')));
   $data = Excel::load($path)->get();



   $key_name = ['list','business','dis_business','project','activ','respons','amount','price_per','unit','unitsap','total','explan','unit_t','year','status'];

   if($data->count() > 0)
   {
     $num = 0;
    foreach($data->toArray() as $key => $value)
    {
      $i = 0;
     foreach($value as $row)
     {
      $insert_data[$num][$key_name[$i]] = $row;
      $num++;
      $i++;
     }
    }
    if(!empty($insert_data))
    {
      for($j = 0; $j < count($insert_data); $j++ ){
        $insert = new Budget;
        $insert->list = $insert_data[$j++]['list'];
        $insert->business = $insert_data[$j++]['business'];
        $insert->dis_business = $insert_data[$j++]['dis_business'];
        $insert->project = $insert_data[$j++]['project'];
        $insert->activ = $insert_data[$j++]['activ'];
        $insert->respons = $insert_data[$j++]['respons'];
        $insert->price_per = $insert_data[$j++]['amount'];
        $insert->price_per = $insert_data[$j++]['price_per'];
        $insert->unit = $insert_data[$j++]['unit'];
        $insert->unitsap = $insert_data[$j++]['unitsap'];
        $insert->total = $insert_data[$j++]['total'];
        $insert->explan = $insert_data[$j++]['explan'];
        $insert->unit_t = $insert_data[$j++]['unit_t'];
        $insert->year = $insert_data[$j++]['year'];
        $insert->status = $insert_data[$j++]['status'];
        $insert->field = Auth::user()->field;
        $insert->office = Auth::user()->office;
        $insert->remark = $insert_data[$j]['remark'];
        $insert->save();
      }

    }
   }
   return back()->with('success', 'Excel Data Imported successfully.');
  }

  public function export_index_budget()
  {
    $data = DB::table('budgets')->get();
    return view('export_excel', ['data' => $data]);
  }

  public function export_budget(Request $request)
  {
    $time_key = $request->date;
    $data = DB::table('budgets')->where('TIME_KEY',$time_key)->get()->toArray();
    // $key_array[] = array('TIME_KEY','ASSET_ID','COST_CENTER','METER_ID','M_UNIT','M_UNIT_PRICE','M_Cost_TOTAL','ACTIVITY_CODE');

    $content = "";
    foreach($data as $value => $key){
      $content .= $key->TIME_KEY."\t";
      $content .= $key->ASSET_ID."\t";
      $content .= $key->COST_CENTER."\t";
      $content .= $key->METER_ID."\t";
      $content .= $key->M_UNIT."\t";
      $content .= $key->M_UNIT_PRICE."\t";
      $content .= $key->M_Cost_TOTAL."\t";
      $content .= $key->ACTIVITY_CODE;
      $content .= "\r\n";
    }
    $fileName = "logs-".$time_key.".txt";

    $headers = [
      'Content-type' => 'text/plain',
      'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName)
      // 'Content-Length' => sizeof($content)
    ];
    //
    return Response::make($content, 200, $headers);
  }

}
