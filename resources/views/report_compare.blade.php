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
    <li class="breadcrumb-item"><a href="#">หน้าแรก</a></li>
    <li class="breadcrumb-item"><a href="#">รายงาน</a></li>
    <li class="breadcrumb-item active" aria-current="page">เปรียบเทียบงบประมาณ</li>
  </ol>
  <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="card">
        <div class="card-header word">
          <i class="fa fa-align-justify"></i> เปรียบเทียบงบประมาณ</div>
          <div class="card-body">
            <form action="{{ route('post_compare') }}" method="post">
                @csrf
              <div class="form-group row">
                @if(Auth::user()->type == "5" || Auth::user()->type == "1")
                <label class="col-md-2 col-form-label">ศูนย์เงินทุน <font color="red">*</font></label>
                <div class="form-group col-sm-4">
                  <div class="input-group">
                    <input class="form-control @error('fund_center') is-invalid @enderror"  type="text" name="fund_center">
                    @error('fund_center')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <label class="col-md-2 col-form-label">ศูนย์ต้นทุน <font color="red">*</font></label>
                <div class="form-group col-sm-4">
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
                <label class="col-md-2 col-form-label">บัญชีรายการภาระผูกพัน</label>
                <div class="form-group col-sm-4">
                  <div class="input-group">
                    <input class="form-control @error('account') is-invalid @enderror"  type="text" name="account">
                    @error('account')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <label class="col-md-2 col-form-label">ปี (พ.ศ.) :</label>
                  <div class="col-md-4">
                    <select class="form-control" name="stat_year">
                      @for($i = (date('Y')+543) ;$i >= (date('Y',strtotime("-3 year"))+543) ; $i--)
                        <option value="{{ $i }}" @if($i == $yy) selected @else '' @endif>{{ $i }}</option>
                      @endfor
                    </select>
                  </div>
              </div>
              <div class="form-group row">
                <div class="form-group col-sm-4">
                  <div class="input-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
                    <form action="{{ route('print_compare') }}" method="post">
                      @csrf
                        <input type="hidden" name="fundcenter" value="{{ $fund }}">
                        <input type="hidden" name="centermoney" value="{{ $center }}">
                        <input type="hidden" name="statyear" value="{{ $yy }}">
                        <input type="hidden" name="account" value="{{ $account }}">
                      &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Export</button>
                    </form>
                  </div>
                </div>
              </div>
          </div>
      </div>
    </div>
    @if(isset($data))
    <table class="table table-responsive-sm table-bordered">
      <tr>
        <th>บัญชีรายการภาระผูกพัน</th>
        <th>ปีงบประมาณ {{ $yy-1 }}</th>
        <th>ปีงบประมาณ {{ $yy }}</th>
      </tr>
      @if(isset($data))
        @php
          $sum1 =0;
          $sum2 =0;
        @endphp
        @foreach($data as $key_acc => $arr_value)
          <tr>
            <td align="center">{{ $key_acc }}</td>
            @if(isset($data_old[$key_acc][$yy-1]))
              @php
                $sum1 += $data_old[$key_acc][$yy-1];
              @endphp
              <td align="right">{{ number_format($data_old[$key_acc][$yy-1],2) }}</td>
            @else
              <td align="center">{{ '-' }}</td>
            @endif
            @if(isset($data[$key_acc][$yy]))
              @php
                $sum2 += $data[$key_acc][$yy];
              @endphp
              <td align="right">{{ number_format($data[$key_acc][$yy],2) }}</td>
            @else
              <td align="center">{{ '-' }}</td>
            @endif
          </tr>
        @endforeach
        <tr>
          <td align="right"><b>Sum</b></td>
          <td align="right"><b>{{ number_format($sum1,2) }}</b></td>
          <td align="right"><b>{{ number_format($sum2,2) }}</b></td>
        </tr>
      @else
      <tr>
        <td align="center" colspan="3">ไม่มีข้อมูล</td>
      </tr>
      @endif
    </table>
    @endif
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
