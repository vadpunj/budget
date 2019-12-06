<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register()
    {
      return view('register');
    }

    public function postregister(Request $request)
    {
      $this->validate($request, [
        'name' => 'required|min:4',
        'emp_id' => 'required|numeric|unique:users'
      ]);
      User::create([
        'name' => $request->name,
        'emp_id' => $request->emp_id
      ]);
      return redirect()->back();
    }
    public function login()
    {
      return view('login');
    }
    public function postlogin(Request $request)
    {
      // $this->validate($request,[
      //    'emp_id'=>'required|numeric',
      //    'password'=>'required'
      // ]);
      // if(!\Auth::attempt(['emp_id' => $request->emp_id,'password'=> $request->password])){
      //   return redirect()->back();
      // }
      // return redirect()->route('home');

      $this->validate($request,[
         'emp_id'=>'required|numeric',
         'password'=>'required'
      ]);
      // ตรวจสอบว่ามีสิทธิ์เข้ามาใช้งานหรือไม่ เช็คจากตารางuserของเราเอง
      $user = User::where('emp_id',$request->emp_id)->first();
      if(!empty($user)){
        // ดึง service จาก ad
        $Controller = new UserController();
        // $urlApi = 'http://192.168.242.164:8010/testservice/services/getservice.php';
        $urlApi = 'http://catdev.cattelecom.com/testservice/services/getservice.php';
        $data_array =  array(
          "ClientKey" => '',
          "ServiceName" => 'AuthenUser',
          "ServiceParams" => array(
                  "emp_code" => $request->emp_id,
                  "pw" => $request->password,
                  ),
          );

        $make_call =  $Controller->callAPI('POST', $urlApi, json_encode($data_array));

        if($make_call  == 'bad request'){
          return redirect()->back()->with('message', 'กรุณาloginใหม่อีกครั้ง'); //user timeout
        }
        $response = json_decode($make_call, true);

        if($response['Result'] == 'Pass'){
          \Auth::login($user);
          return redirect()->route('home'); // รหัส login ผ่าน
        }else{
          return redirect()->back()->with('message', 'รหัสผ่านไม่ถูกต้อง'); //รหัสผิด
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
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
         'APIKEY: 111111111111111111111',
         'Content-Type: application/json',
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

      // EXECUTE:
      $result = curl_exec($curl);
      // dd($result);
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
}
