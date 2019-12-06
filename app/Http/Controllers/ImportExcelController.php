<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
// use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Log_user;
use App\Electric;
use App\Water;
class ImportExcelController extends Controller
{
    public function index_electric()
    {
      $data = Electric::groupBy('TIME_KEY')
        ->selectRaw('TIME_KEY,sum(M_UNIT) as M_UNIT, sum(M_UNIT_PRICE) as M_UNIT_PRICE,sum(M_Cost_TOTAL) as M_Cost_TOTAL')
        ->orderBy('TIME_KEY','DESC')
        ->first();
      return view('import_excel', ['type' => 'electric','data' => $data]);
    }

    public function import_electric(Request $request)
    {
      set_time_limit(0);
      $this->validate($request, [
        'select_file'  => 'required|mimes:xls,xlsx',
        'time_key' => 'required|numeric'
      ]);
      // delete ก่อน insert
      $delete_data = Electric::where('TIME_KEY',$request->time_key)->delete();

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     $data = Excel::load($path)->get();

     $insert_log = new Log_user;
     $insert_log->user_name = Auth::user()->name;
     // $insert_log->user_name = 'phats';
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
          $insert = new Electric;
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

    public function index_water()
    {
      $data = Water::groupBy('TIME_KEY')
        ->selectRaw('TIME_KEY,sum(M_UNIT) as M_UNIT, sum(M_UNIT_PRICE) as M_UNIT_PRICE,sum(M_Cost_TOTAL) as M_Cost_TOTAL')
        ->orderBy('TIME_KEY','DESC')
        ->first();
      return view('import_excel', ['type' => 'water','data' => $data]);
    }

    public function import_water(Request $request)
    {
      set_time_limit(0);
     $this->validate($request, [
      'select_file'  => 'required|mimes:xls,xlsx',
      'time_key' => 'required|numeric'
     ]);

     $delete_data = Water::where('TIME_KEY',$request->time_key)->delete();

     $path = $request->file('select_file')->getRealPath();
     $name = $request->file('select_file')->getClientOriginalName();
     $pathreal = Storage::disk('log')->getAdapter()->getPathPrefix();
     $data = Excel::load($path)->get();
     // เก็บข้อมูลว่าใครเป็นคน insert file เข้าระบบ
     $insert_log = new Log_user;
     $insert_log->user_name = Auth::user()->name;
     // $insert_log->user_name = 'phats';
     $insert_log->path = $pathreal.$name;
     $insert_log->type_log = 'water';
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
          $insert = new Water;
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
