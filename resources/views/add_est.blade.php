@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Add page</title>
@endsection

@section('css')
  <!-- <link href="{{ asset('admin/node_modules/@coreui/icons/css/coreui-icons.min.css') }}" rel="stylesheet"> -->
  <link href="{{ asset('admin/node_modules/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css') }}" rel="stylesheet">
  <!-- Global site tag (gtag.js) - Google Analytics-->
  <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>
  <link href="{{ asset('admin/css/jquery.dataTables.css') }}" rel="stylesheet">
  <script src="{{ asset('admin/js/jquery-1.12.0.js') }}"></script>
  <style>
    .word {
      color: #fff !important;
    }
  </style>
@endsection

@section('content')
  <main class="main">
    <!-- Breadcrumb-->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">เพิ่มข้อมูลงบประมาณทำการ</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
     <div class="row">
      <div class="col-lg-12">
        <form class="" action="{{ route('insert_est') }}" method="post">
          @if($message = Session::get('success'))
          <div class="alert alert-success alert-block">
           <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
          </div>
          @endif
          <div class="card">
          <div class="card-header word">
            <i class="fa fa-align-justify"></i> ข้อมูลผู้ขอ</div>
            <div class="card-body">
                @csrf
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ปีงบประมาณ(พ.ศ.)</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control @error('stat_year') is-invalid @enderror" type="text" name="stat_year">
                      @error('stat_year')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <label class="col-md-2 col-form-label">สายงาน</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control" type="text" name="field" value="{{\Auth::user()->field}}" disabled>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ฝ่าย/สำนักงาน/ศูนย์</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control" type="text" name="office" value="{{\Auth::user()->office}}" disabled>
                  </div>
                  <label class="col-md-2 col-form-label">ส่วน/สค.</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control" type="text" name="part" value="{{\Auth::user()->part}}" disabled>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ชื่อผู้ขอ</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('name_reqs') is-invalid @enderror" type="text" name="name_reqs" value="{{\Auth::user()->name}}">
                    @error('name_reqs')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <label class="col-md-2 col-form-label">เบอร์ติดต่อ</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('phone') is-invalid @enderror" type="text" name="phone" placeholder="ตัวเลขเท่านั้น">
                    @error('phone')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ศูนยเงินทุน</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control" type="text" name="center_money" value="{{\Auth::user()->center_money}}" disabled>
                  </div>
                </div>
          </div>
         </div>
           <div style="overflow-x: scroll;">
           <table class="table table-responsive-sm table-bordered" style="width: 150%" >
             <thead>
               <tr>
                 <th>รหัสบัญชี</th>
                 <th>หมวด/ประเภทรายจ่าย</th>
                 <th>{{'รายจ่ายจริงปี '.(date("Y",strtotime("-4 year"))+543)}}</th>
                 <th>{{'รายจ่ายจริงปี '.(date("Y",strtotime("-3 year"))+543)}}</th>
                 <th>รายจ่ายจริง</th>
                 <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-2 year"))+543)}}</th>
                 <th>{{'งบประมาณปี '.(date("Y",strtotime("-1 year"))+543)}}</th>
                 <th>{{'งบประมาณขอตั้งปี '.(date("Y")+543)}}</th>
                 <th>{{'% ผลรวมที่เพิ่มขึ้นจากปี '.(date("Y",strtotime("-1 year"))+543)}}</th>
                 <th>คำชี้แจง</th>
               </tr>
             </thead>
             <tbody>
                @foreach($now as $arr_key => $arr_val)
                  @foreach($arr_val as $key => $value)
                    <tr>
                      <td>{{ $key }}</td>
                      <td>{{ Func::get_account($key) }}</td>
                      @if($year4[date("Y",strtotime("-4 year"))+543][$key] != 0)
                        <td align="right">{{ number_format($year4[date("Y",strtotime("-4 year"))+543][$key],2) }}</td>
                      @elseif($year4[date("Y",strtotime("-4 year"))+543][$key] == 0)
                        <td align="center">{{ '-' }}</td>
                      @endif
                      @if($year3[date("Y",strtotime("-3 year"))+543][$key] != 0)
                        <td align="right">{{ number_format($year3[date("Y",strtotime("-3 year"))+543][$key],2) }}</td>
                      @elseif($year3[date("Y",strtotime("-3 year"))+543][$key] == 0)
                        <td align="center">{{ '-' }}</td>
                      @endif
                      <td></td>
                      @if($year2[date("Y",strtotime("-2 year"))+543][$key] != 0)
                        <td align="right">{{ number_format($year2[date("Y",strtotime("-2 year"))+543][$key],2) }}</td>
                      @elseif($year2[date("Y",strtotime("-2 year"))+543][$key] == 0)
                        <td align="center">{{ '-' }}</td>
                      @endif
                      @if($year1[date("Y",strtotime("-1 year"))+543][$key] != 0)
                        <td align="right">{{ number_format($year1[date("Y",strtotime("-1 year"))+543][$key],2) }}</td>
                      @elseif($year1[date("Y",strtotime("-1 year"))+543][$key] == 0)
                        <td align="center">{{ '-' }}</td>
                      @endif
                      @if($now[date("Y")+543][$key] != 0)
                        <td align="center">
                          <input type="text" name="budget[{{$key}}]" value="{{$now[date('Y')+543][$key]}}">
                        </td>
                      @elseif($now[date("Y")+543][$key] == 0)
                        <td align="center"> <input type="text" name="budget[{{$key}}]" >
                        </td>
                      @endif
                      <td align="center"></td>
                      @if($explan[date("Y")+543][$key] == null)
                        <td> <textarea name="explan[{{$key}}]"></textarea>
                        </td>
                      @elseif($explan[date("Y")+543][$key] != null)
                        <td> <textarea name="explan[{{$key}}]">{{ $explan[date("Y")+543][$key] }}</textarea>
                        </td>
                      @endif
                   </tr>
                  @endforeach
                @endforeach
             </tbody>
           </table>

           <div class="form-group row">
             <div class="col-md-2 form-group">
               <button class="btn btn-primary" type="submit">Submit</button>
             </div>
           </div>
         </div>
        </form>
       </div>
     </div>
     </div>
   </div>
 </main>

@endsection

@section('js')
<script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('admin/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
<script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#myTable').DataTable({
      scrollX:true
    });
  });
  </script>
@endsection
