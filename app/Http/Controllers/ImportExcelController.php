<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
// use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Log_user;
use App\Budget;

class ImportExcelController extends Controller
{
    public function index_budget()
    {

      // $data = Budget::where('year',date('Y'))->get()->toArray();
      // $data = DB::table('budgets')
      //       ->select(DB::raw('sum(money) as money,business_process,product,functional_area,segment'))
      //       ->whereBetween('date', date('Y'))
      //       ->groupBy('business_process','product','functional_area','segment')
      //       ->get()
      //       ->toArray();
      return view('import_excel');
    }

    public function import_budget(Request $request)
    {
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xlsx'
      ]);

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     $data = Excel::load($path)->get();

     $insert_log = new Log_user;
     $insert_log->user_id = Auth::user()->emp_id;
     $insert_log->path = $pathreal.$name;
     $insert_log->type_log = 'electric';
     $insert_log->save();

     $key_name = ['year','branch','list','detail','money','remark'];

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
          $insert->year = $insert_data[$j++]['year'];
          $insert->branch = $insert_data[$j++]['branch'];
          $insert->list = $insert_data[$j++]['list'];
          $insert->detail = $insert_data[$j++]['detail'];
          $insert->money = round($insert_data[$j++]['money'],2);
          $insert->remark = $insert_data[$j]['remark'];
          $insert->save();
        }

      }
     }
     return back()->with('success', 'Excel Data Imported successfully.');
    }

    public function ajax_data()
    {
      $year = $_POST['year'];
      $data = Budget::where('year',$year)->get()->toArray();

      if(!empty($data)){
          return response()->json(['success' => $data]);
        }else{
          return response()->json(['error']);
          return response()->json(['error' => '']);
        }
    }

}
