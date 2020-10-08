@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>View Estimate</title>
@endsection

@section('css')
  <!-- <link href="{{ asset('admin/node_modules/@coreui/icons/css/coreui-icons.min.css') }}" rel="stylesheet"> -->
  <link href="{{ asset('admin/node_modules/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/jquery.dataTables.css') }}" rel="stylesheet">

  <!-- Global site tag (gtag.js) - Google Analytics-->
  <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>
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
    <li class="breadcrumb-item active">ดูข้อมูลงบประมาณ</li>
  </ol>
  <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="row">
          <div class="col-lg-12">
            <form action="{{ route('post_view_estimate') }}" method="post">
                @csrf
            <div class="form-group row">
              <label class="col-md-2 col-form-label">บัญชีรายการภาระผูกพัน</label>
              <div class="col-md-3">
                <input class="form-control @error('account') is-invalid @enderror"  type="text" name="account">
                @error('account')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
              @if(Auth::user()->type == "5" || Auth::user()->type == "1")
              <label class="col-md-2 col-form-label">ศูนย์ต้นทุน</label>
              <div class="col-md-3">
                <div class="input-group">
                  <input class="form-control @error('center_money') is-invalid @enderror"  type="text" name="center_money">
                  @error('center_money')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              @endif
            </div>
              <button class="btn btn-primary" type="submit">Submit</button>
            </form>
          </div>
      </div>
    </div>
  </div>

  <div class="card-body">
    <table class="table table-responsive-sm table-bordered">
      <tr>
        <th>บัญชีรายการภาระผูกพัน</th>
        <th>ปีงบประมาณ {{ date("Y")+542 }}</th>
        <th>ปีงบประมาณ {{ date("Y")+543 }}</th>
      </tr>
      @if(isset($data))
        @foreach($data as $key_acc => $arr_value)
          <tr>
            <td align="center">{{ $key_acc }}</td>
            @if(isset($data[$key_acc][date("Y")+542]))
              <td align="right">{{ number_format($data[$key_acc][date("Y")+542],2) }}</td>
            @else
              <td align="center">{{ '-' }}</td>
            @endif
            @if(isset($data[$key_acc][date("Y")+543]))
              <td align="right">{{ number_format($data[$key_acc][date("Y")+543],2) }}</td>
            @else
              <td align="center">{{ '-' }}</td>
            @endif
          </tr>
        @endforeach
      @else
      <tr>
        <td align="center" colspan="3">ไม่มีข้อมูล</td>
      </tr>
      @endif
      </tr>
    </table>
  </div>


</main>
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/jquery.dataTables.js') }}"></script>
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
