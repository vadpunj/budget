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
      $data = Budget::get();
      return view('import_excel', ['data' => $data]);
    }

    public function import_budget(Request $request)
    {
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xls,xlsx',
        'time_key' => 'required|numeric'
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

     $key_name = ['TIME_KEY','ASSET_ID','COST_CENTER','METER_ID','M_UNIT','M_UNIT_PRICE','M_Cost_TOTAL','ACTIVITY_CODE'];

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
          $insert->TIME_KEY = $insert_data[$j++]['TIME_KEY'];
          $insert->ASSET_ID = $insert_data[$j++]['ASSET_ID'];
          $insert->COST_CENTER = $insert_data[$j++]['COST_CENTER'];
          $insert->METER_ID = $insert_data[$j++]['METER_ID'];
          $insert->M_UNIT = $insert_data[$j++]['M_UNIT'];
          $insert->M_UNIT_PRICE = round($insert_data[$j++]['M_UNIT_PRICE'],2);
          $insert->M_Cost_TOTAL = round($insert_data[$j++]['M_Cost_TOTAL'],2);
          $insert->ACTIVITY_CODE = $insert_data[$j]['ACTIVITY_CODE'];
          $insert->save();
        }

      }
     }
     return back()->with('success', 'Excel Data Imported successfully.');
    }

}
