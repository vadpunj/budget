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
  <script src="{{ asset('admin/js/jquery-1.12.0.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


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
      <li class="breadcrumb-item active">สถานะข้อมูลงบประมาณทำการ</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์ต้นทุน</th>
                <th>ตั้งงบ</th>
                <th>ฝ่าย/เขต อนุมัติ</th>
                <th>วง. อนุมัติ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($status as $key => $value)
              <tr>
                <td align="center">{{ $value["year"] }}</td>
                <td align="center">{{ $value["center_money"] }}</td>
                @if($value["status"] == NULL || $value["status"] == "1" || $value["status"] == "0")
                  <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                @elseif($value["status"] == "4")
                  <td align="center">{{ 'ปรับแก้งบประมาณ' }}</td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
                @if($value["status"] == "0" || $value["status"] == "1")
                  <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
                @if($value["status"] == "1")
                  <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
              </tr>
              @endforeach

            </tbody>
          </table>
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
      $('.myTable').DataTable({
        select:true,
      });
  </script>

@endsection
