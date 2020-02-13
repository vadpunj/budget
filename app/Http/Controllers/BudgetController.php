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
    if($number > 0)
    {
      $data = new User_request;
      $data->stat_year = $_POST["stat_year"];
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = $_POST["part"];
      $data->name = $_POST["name_reqs"];
      $data->phone = $_POST["phone"];
      $data->center_money = Auth::user()->center_money;
      $data->type = 'งบลงทุน';
      $data->save();

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
      return view('edit_bud',['user_req' => $user_req,'budget' => $budget]);
  }

  public function post_edit_data(Request $request)
  {
    $number = count($request->list_old);
// dd($request->list_old[2]);
    if($number > 0){
      $data = User_request::find($request->id);
      $data->stat_year = $request->stat_year;
      $data->field = Auth::user()->field;
      $data->office = Auth::user()->office;
      $data->part = $request->part;
      $data->name = $request->name_reqs;
      $data->phone = $request->phone;
      $data->type = 'งบลงทุน';
      $data->center_money = Auth::user()->center_money;
      $data->update();

      // update ข้อมูลเก่า
      for($j=0 ; $j<$number ; $j++){
        if($request->list_old[$j] == null){
          $delete = Budget::find($request->id_old[$j]);
          $delete->delete();
        }else{
          $update = Budget::find($request->id_old[$j]);
          $update->list = trim($request->list_old[$j]);
          $update->business = trim($request->business_old[$j]);
          $update->dis_business = trim($request->dis_business_old[$j]);
          $update->project = trim($request->project_old[$j]);
          $update->activ = trim($request->activ_old[$j]);
          $update->respons = trim($request->respons_old[$j]);
          $update->amount = trim($request->amount_old[$j]);
          $update->price_per = trim($request->price_per_old[$j]);
          $update->unit = trim($request->unit_old[$j]);
          $update->unitsap = trim($request->unitsap_old[$j]);
          $update->total = trim($request->total_old[$j]);
          $update->explan = trim($request->explan_old[$j]);
          $update->unit_t = trim($request->unit_t_old[$j]);
          $update->year = trim($request->year_old[$j]);
          $update->status = trim($request->status_old[$j]);
          $update->field =  Auth::user()->field;
          $update->office = Auth::user()->office;
          $update->user_request_id = $request->id;
          $update->update();
        }
      }
    }

    if(!is_null($request->list)){
      // insert ข้อมูลใหม่
      for($i=0; $i<count($request->list); $i++)
      {
        $insert = new Budget;
        $insert->list = $request->list[$i];
        $insert->business = trim($request->business[$i]);
        $insert->dis_business = trim($request->dis_business[$i]);
        $insert->project = trim($request->project[$i]);
        $insert->activ = trim($request->activ[$i]);
        $insert->respons = trim($request->respons[$i]);
        $insert->amount = trim($request->amount[$i]);
        $insert->price_per = trim($request->price_per[$i]);
        $insert->unit = trim($request->unit[$i]);
        $insert->unitsap = trim($request->unitsap[$i]);
        $insert->total = trim($request->total[$i]);
        $insert->explan = trim($request->explan[$i]);
        $insert->unit_t = trim($request->unit_t[$i]);
        $insert->year = trim($request->year[$i]);
        $insert->status = trim($request->status[$i]);
        $insert->field =  Auth::user()->field;
        $insert->office = Auth::user()->office;
        $insert->user_request_id = $request->id;
        $insert->save();
      }
    }
    // dd(2323);
      return back()->with('success', 'Successful.');
  }

  public function import_index_budget()
  {

    $data = Budget::where('year',(date('Y')+543))->get()->toArray();

    return view('import_bud',['data' => $data]);
  }

  public function import_budget(Request $request)
  {
    config(['excel.import.heading' => 'original' ]);
    set_time_limit(0);
    // $this->validate($request, [
    //   'select_file'  => 'required|mimes:xlsx'
    // ]);

   $path = $request->file('select_file')->getRealPath();
   $name = $request->file('select_file')->getClientOriginalName();
   $type = $request->file('select_file')->getClientOriginalExtension();
   // $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
   if($type == 'xlsx'){
     Storage::disk('log')->put($name, File::get($request->file('select_file')));
     $data = Excel::load($path)->get();

// dd($data);

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
      // dd($insert_data);
      if(!empty($insert_data))
      {
        $add = new User_request;
        $add->stat_year = date('Y')+543;
        $add->field = Auth::user()->field;
        $add->office = Auth::user()->office;
        $add->part = Auth::user()->part;
        $add->name = Auth::user()->name;
        $add->phone = Auth::user()->tel;
        $add->type = 'งบลงทุน';
        $add->center_money = Auth::user()->center_money;
        $add->save();

        for($j = 0; $j < count($insert_data); $j++ ){
          $insert = new Budget;
          $insert->list = $insert_data[$j++]['list'];
          $insert->business = $insert_data[$j++]['business'];
          $insert->dis_business = $insert_data[$j++]['dis_business'];
          $insert->project = $insert_data[$j++]['project'];
          $insert->activ = $insert_data[$j++]['activ'];
          $insert->respons = $insert_data[$j++]['respons'];
          $insert->amount = $insert_data[$j++]['amount'];
          $insert->price_per = round($insert_data[$j++]['price_per']);
          $insert->unit = $insert_data[$j++]['unit'];
          $insert->unitsap = $insert_data[$j++]['unitsap'];
          $insert->total = round($insert_data[$j++]['total']);
          $insert->explan = $insert_data[$j++]['explan'];
          $insert->unit_t = $insert_data[$j++]['unit_t'];
          $insert->year = $insert_data[$j++]['year'];
          $insert->status = $insert_data[$j]['status'];
          $insert->user_request_id = $add->id;
          $insert->field = Auth::user()->field;
          $insert->office = Auth::user()->office;
          $insert->save();
        }

      }
     }
     return back()->with('success', 'Excel Data Imported successfully.');
   }else{
     dd("Can not insert data");
   }

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
