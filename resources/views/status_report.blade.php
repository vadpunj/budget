@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>View page</title>
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
  <link rel="stylesheet" href="{{ asset('admin/css/bootstrap-select.min.css')}}">
  <script src="{{ asset('admin/js/jquery-1.12.0.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


  <style>
    .word {
      color: #fff !important;
    }
    .selectpicker+button {
      color: #23282c;
      background-color: #fff;
      border-color: #e4e7ea;
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
      <li class="breadcrumb-item active">ขั้นตอนงบประมาณทำการ</li>
    </ol>
    <!-- end breadcrumb -->
    <div class="card-body">
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          @if(Auth::user()->type == 5)
          <form action="{{ route('post_status') }}" method="post">
            @csrf
          <div class="form-group row">
            <label class="col-md-2 col-form-label" for="date-input">ชื่อฝ่าย(ย่อ) : <font color="red">*</font></label>
              <div class="col-md-3">
                <input class="form-control"  type="text" name="cost_title">

              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
          @endif
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ฝ่าย</th>
                <th>ศูนย์ต้นทุน</th>
                <th>ส่วน</th>
                <th>งบประมาณ</th>
                <th>ตั้งงบ</th>
                <th>ฝ่าย/เขต อนุมัติ</th>
                <th>วง. อนุมัติ</th>
              </tr>
            </thead>
            <tbody>
              @if(isset($status))
                @foreach($status as $key => $arr_value)
                  @foreach($arr_value as $key2 => $value)
                  <tr>
                    <td align="center">{{ $value["stat_year"] }}</td>
                    <td align="center">{{ $value["cost_title"] }}</td>
                    <td align="center">{{ $value["center_money"] }}</td>
                    <td>{{ Func::get_name_costcenter($value["center_money"]) }}</td>
                    <td align="right">{{ number_format($value["budget"],2) }}</td>
                    @if($value["status"] == "5")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">{{ '-' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "3")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">{{ 'วง.ขอแก้ไขงบ' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "1")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                    @elseif($value["status"] == "4")
                      <td align="center">{{ 'ปรับแก้งบประมาณ' }}</td>
                      <td align="center">{{ '-' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "0")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">{{ '-' }}</td>
                    @endif
                  </tr>
                  @endforeach
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
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
      <!-- Latest compiled and minified JavaScript -->
  <script src="{{ asset('admin/js/bootstrap-select.min.js')}}"></script>
  <script type="text/javascript">
      $('.myTable').DataTable({
        select:true,
      });
  </script>

@endsection
