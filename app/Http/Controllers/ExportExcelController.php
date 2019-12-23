<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use App\Log_user;
use App\Budget;
use Response;

class ExportExcelController extends Controller
{
    public function index_budget()
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
        if($key->TIME_KEY == $key->M_UNIT){
          $content .= "\n".$key->TIME_KEY;
          $content .= "\r";
        }else{
          $content .= "\n".$key->TIME_KEY."\t";
          $content .= $key->ASSET_ID."\t";
          $content .= $key->COST_CENTER."\t";
          $content .= $key->METER_ID."\t";
          $content .= $key->M_UNIT."\t";
          $content .= $key->M_UNIT_PRICE."\t";
          $content .= $key->M_Cost_TOTAL."\t";
          $content .= $key->ACTIVITY_CODE;
          $content .= "\r";
        }
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
