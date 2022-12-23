<?php

namespace App\Http\Controllers;

use DB;
use Func;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Structure;
use App\Shutdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register()
    {
      $role = Func::get_role_all();
      // dd($role);
      return view('register',['role' => $role]);
    }

    public function postregister(Request $request)
    {
      // dd($request->all());
      $this->validate($request, [
        'name' => 'required|min:4',
        'emp_id' => 'required|numeric',
        'cost_id' => 'required|min:4',
        'phone' => 'required|numeric'
      ]);
      // $pw = str_random(8);
      User::create([
        'name' => $request->name,
        'emp_id' => $request->emp_id,
        'pwd' => str_random(8),
        'type' => $request->type,
        'center_money' => strtoupper($request->cost_id),
        'tel' => $request->phone,
        'user_id' => Auth::user()->emp_id
      ]);
      // return redirect()->back();
      return back()->with('success', 'เพิ่มผู้ใช้แล้ว');
    }
    public function login()
    {
      return view('login');
    }
//     public function postlogin(Request $request) // login ใช้ AD เก่า
//     {
//       // $this->validate($request,[
//       //    'emp_id'=>'required|numeric',
//       //    'password'=>'required'
//       // ]);
//       // if(!\Auth::attempt(['emp_id' => $request->emp_id,'password'=> $request->password])){
//       //   return redirect()->back();
//       // }
//       // return redirect()->route('home');
//
//       $this->validate($request,[
//          'emp_id'=>'required|numeric',
//          'password'=>'required'
//       ]);
//       $shut = Func::rang_shutdown(date('Y-m-d'));
// // dd($shut);
//       // ตรวจสอบว่ามีสิทธิ์เข้ามาใช้งานหรือไม่ เช็คจากตารางuserของเราเอง
//       $user = User::where('emp_id',$request->emp_id)->first();
//       if(!empty($user)){
//         // ดึง service จาก ad
//         $Controller = new UserController();
//         // $urlApi = 'http://192.168.242.164:8010/testservice/services/getservice.php';
//         $urlApi = 'http://catdev.cattelecom.com/testservice/services/getservice.php';
//         // $urlApi = 'http://192.168.242.164/getservice.php';
//         $data_array =  array(
//           "ClientKey" => '',
//           "ServiceName" => 'AuthenUser',
//           "ServiceParams" => array(
//                   "emp_code" => $request->emp_id,
//                   "pw" => $request->password,
//                   ),
//           );
//
//         $make_call =  $Controller->callAPI('POST', $urlApi, json_encode($data_array));
//         // dd($make_call);
//         $key = json_decode($make_call, true);
// // dd($key);
//         if($make_call  == 'bad request'){
//           return redirect()->back()->with('message', 'กรุณาloginใหม่อีกครั้ง'); //user timeout
//         }
//         $response = json_decode($make_call, true);
//         // dd($response);
//         if($response['Result'] == 'Pass'){
//           if(is_null($user->field)){ //เคสlogin ครั้งแรก
//             $struc = Structure::where('CostCenterID',$user->center_money)->first();
//             // dd(Func::get_name_costcenter($user->center_money));
//             $update = DB::table('users')
//                 ->where('emp_id',$request->emp_id)
//                 ->update(['field' => Func::get_name_costcenter_by_divID($struc->FundsCenterID),'cost_title' => $struc->CostCenterTitle,'fund_center' => $struc->FundID,
//                 'division_center' => $struc->FundsCenterID,'office'=> Func::get_office_name($struc->FundID) ,'part' => Func::get_name_costcenter($user->center_money) ,'nt' => $struc->NT,
//                 'updated_at' => date('Y-m-d H:i:s')]);
//           }
//
// // dd($shut);
//           \Auth::login($user);
//
//           if((Auth::user()->type == 2 || Auth::user()->type == 3 || Auth::user()->type == 4) && $shut == false){
//             // dd(24343);
//             Auth::logout();
//             return redirect()->back()->with('message', 'อยู่ในช่วงปิดระบบ ไม่สามารถเข้าใช้งานได้');
//             // dd(24242);
//           }else{
//             return redirect()->route('dashboard'); // รหัส login ผ่าน
//
//           }
//           // if((Auth::user()->type <> "5" || Auth::user()->type <> "1") && $shut == true){
//           //   return redirect()->route('close');
//           // }
//         }else{
//           return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง'); //รหัสผิด
//         }
// //         if($response['Result'] == 'Pass'){
// //           if(is_null($user->field)){
// //             $key_json = json_decode($make_call);
// //             $key = $key_json->ClientKey;
// //
// //             $data_profile =  array(
// //               "ClientKey" => $key,
// //               "ServiceName" => 'GetQrUserProfile',
// //               "ServiceParams" => array(
// //                       "emp_code" => $request->emp_id,
// //                       "pw" => $request->password,
// //                       ),
// //               );
// //             $make_data =  $Controller->callAPI('POST', $urlApi, json_encode($data_profile));
// //             // dd($make_data);
// //             $jsodata = json_decode($make_data);
// //
// //             if(empty($jsodata->div_name)){
// //               $line = Structure::where('CostCenterName',$jsodata->dept_name)->first();
// //               $update = DB::table('users')
// //                   ->where('emp_id',$request->emp_id)
// //                   ->update(['field' => Func::get_name_costcenter_by_divID($line->FundsCenterID),'cost_title' => $line->CostCenterTitle,'center_money' => null,'fund_center' => $line->FundID,
// //                   'division_center' => $line->FundsCenterID,'office'=> $jsodata->dept_name ,'part' => null ,'tel' => $jsodata->phone_no,'nt' => $line->NT,
// //                   'updated_at' => date('Y-m-d H:i:s')]);
// //               // dd('23');
// //             }
// //             $line = Structure::where('CostCenterName',$jsodata->div_name)->first();
// //             $update = DB::table('users')
// //                 ->where('emp_id',$request->emp_id)
// //                 ->update(['field' => Func::get_name_costcenter_by_divID($line->FundsCenterID),'cost_title' => $line->CostCenterTitle,'center_money' => $line->CostCenterID,'fund_center' => $line->FundID,
// //                 'division_center' => $line->FundsCenterID,'office'=> $jsodata->dept_name ,'part' => $jsodata->div_name ,'tel' => $jsodata->phone_no,'nt' => $line->NT,
// //                 'updated_at' => date('Y-m-d H:i:s')]);
// //           }
// //
// // // dd($shut);
// //           \Auth::login($user);
// //
// //           if((Auth::user()->type == 2 || Auth::user()->type == 3 || Auth::user()->type == 4) && $shut == false){
// //             // dd(24343);
// //             Auth::logout();
// //             return redirect()->back()->with('message', 'อยู่ในช่วงปิดระบบ ไม่สามารถเข้าใช้งานได้');
// //             // dd(24242);
// //           }else{
// //             return redirect()->route('dashboard'); // รหัส login ผ่าน
// //
// //           }
// //           // if((Auth::user()->type <> "5" || Auth::user()->type <> "1") && $shut == true){
// //           //   return redirect()->route('close');
// //           // }
// //         }else{
// //           return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง'); //รหัสผิด
// //         }
//       }else{
//         return redirect()->back()->with('message', 'คุณไม่มีสิทธิ์เข้าใช้งานระบบนี้'); //ไม่มีสิทธิ์
//       }
//     }
public function change_pw(Request $request)
{
  $this->validate($request,[
     'old'=>'required|min:8',
     'new'=>'required|min:8',
     'renew'=>'required|min:8'
  ]);
  // dd($request->all());

  $user = User::where('emp_id',$request->emp_id)->first();
  if(($user->pwd == $request->old || Hash::check($request->password, $user->pwd))&& $request->new == $request->renew){
    $update = DB::table('users')
       ->where('emp_id',$request->emp_id)
       ->update(['pwd' => Hash::make($request->new)]);
       return redirect()->route('dashboard')->with('jsAlert', 'บันทึกเรียบร้อย');
    // return redirect()->back()->with('message', 'บันทึกเรียบร้อย');
  }else{
    return redirect()->route('dashboard')->with('jsAlert', 'รหัสผ่านไม่ถูกต้อง');
    // return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง');
  }
}

public function postlogin(Request $request) // login ใช้ AD เก่า
{

  $this->validate($request,[
     'emp_id'=>'required',
     'password'=>'required'
  ]);
  $shut = Func::rang_shutdown(date('Y-m-d'));
// dd($shut);
  // ตรวจสอบว่ามีสิทธิ์เข้ามาใช้งานหรือไม่ เช็คจากตารางuserของเราเอง
  $user = User::where('emp_id',$request->emp_id)->first();
  // \Auth::login($user);
// dd($user->emp_id);
  if($user->emp_id == 'Admin'){ // Admin ไม่ต้องเช็คอะไรเลย login pwอะไรก็ได้เข้าใช้ได้หมด
    \Auth::login($user);
    return redirect()->route('dashboard');
  }



  if(!empty($user)){
    if(is_null($user->field)){ //เคสlogin ครั้งแรก

        // dd(342);
      if($request->password == $user->pwd){
        $struc = Structure::where('CostCenterID',$user->center_money)->first();
        // dd(Func::get_name_costcenter($user->center_money));
        $update = DB::table('users')
            ->where('emp_id',$request->emp_id)
            ->update(['field' => Func::get_name_costcenter_by_divID($struc->FundsCenterID),'cost_title' => $struc->CostCenterTitle,'fund_center' => $struc->FundID,
            'division_center' => $struc->FundsCenterID,'office'=> Func::get_office_name($struc->FundID) ,'part' => Func::get_name_costcenter($user->center_money) ,'nt' => $struc->NT,
            'updated_at' => date('Y-m-d H:i:s')]);
        \Auth::login($user);
        return redirect()->route('dashboard');
      }else{
        // dd(4234);
        return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง');
      }
    }else{
      // dd(Hash::check($request->password, $user->pwd));
      if(Hash::check($request->password, $user->pwd) || $request->password == $user->pwd){
        \Auth::login($user);
        return redirect()->route('dashboard');
      }else{
          return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง');
      }
    }

  }else{
    return redirect()->back()->with('message', 'คุณไม่มีสิทธิ์เข้าใช้งานระบบนี้'); //ไม่มีสิทธิ์
  }
}
    public static function callAPI($method, $url, $data){
      $curl = curl_init();
      switch ($method){
         case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
         case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
         default:
            if ($data)
               $url = sprintf("%s?%s", $url, http_build_query($data));
      }

      // OPTIONS:
      curl_setopt($curl, CURLOPT_URL, trim($url));
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
         'APIKEY: 111111111111111111111',
         'Content-Type: application/json',
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
      // EXECUTE:

      $result = curl_exec($curl);

      if(!$result){
        die("Connection Failure");
      }

      curl_close($curl);
      return $result;
    }

    public function logout()
    {
        \Auth::logout();
        return redirect()->route('login');
    }

    public function list_user()
    {
      $list = User::get();
      $role = Func::get_role_all();
      return view('list_user',['list' => $list,'roles' => $role]);
    }

    public function edit_user(Request $request)
    {
      // dd($request->all());
      $this->validate($request, [
        'name' => 'required|min:4',
        'emp_id' => 'required|numeric'
      ]);

      $update = User::find($request->id);
      $update->name = $request->name;
      $update->emp_id = $request->emp_id;
      $update->field = Func::get_field_name($request->division_center);
      $update->office = Func::get_office_name($request->fund_center);
      $name = Func::get_part_name($request->center_money);
      $update->part = $name->CostCenterName;
      $update->cost_title =  $name->CostCenterTitle;
      if($request->center_money == NULL){
        $update->part = null;
        $update->cost_title =  null;
      }
      $update->center_money = $request->center_money;
      $update->type = $request->type;
      $update->tel = $request->tel;
      $update->fund_center = $request->fund_center;
      $update->division_center = $request->division_center;
      $update->update();

      if($update){
        return back()->with('success', 'บันทึกข้อมูลแล้ว');
      }
      // return back()->with('success', 'Update Successful');
    }

    public function delete_user(Request $request)
    {
      $delete = User::find($request->id);
      $delete->delete();
      if($delete){
        return back()->with('success', 'ลบข้อมูลแล้ว');
      }
      // return back()->with('success', 'Delete Successful');
    }
    public function shutdown()
    {
      $shut = Func::rang_shutdown(date('Y-m-d'));
      $status = '';
      if($shut == false){
        $status = 'กำลังอยู่ในช่วงปิดระบบ';
      }
      return view('shutdown',['status' => $status]);
    }
    public function post_shutdown(Request $request)
    {
      $this->validate($request, [
        'start_date' => 'required',
        'end_date' => 'required'
      ]);
// dd($request->all());
      $insert = new Shutdown;
      $insert->start_date = date('Y-m-d',strtotime($request->start_date));
      $insert->end_date = date('Y-m-d',strtotime($request->end_date));
      $insert->user_id = Auth::user()->emp_id;
      $insert->save();

      return back()->with('success', 'เพิ่มข้อมูลแล้ว');
    }
}
