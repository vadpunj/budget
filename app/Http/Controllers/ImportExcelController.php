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




    public function ajax_data()
    {
      $year = $_POST['year']+543;
      $data = Budget::where('year',$year)->get()->toArray();

      if(!empty($data)){
          return response()->json(['success' => $data]);
        }else{
          return response()->json(['error']);
          // return response()->json(['error' => '']);
        }
    }

}
