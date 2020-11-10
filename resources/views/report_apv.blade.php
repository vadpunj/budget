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
    <li class="breadcrumb-item"><a href="#">หน้าแรก</a></li>
    <li class="breadcrumb-item"><a href="#">รายงาน</a></li>
    <li class="breadcrumb-item active" aria-current="page">สถานะอนุมัติงบประมาณ</li>
</ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="card">
        <div class="card-header word">
          <i class="fa fa-align-justify"></i> เปรียบเทียบงบประมาณ</div>
          <div class="card-body">
          <form action="{{ route('post_report_apv') }}" method="post">
            @csrf
            <div class="form-group row">
              <label class="col-md-2 col-form-label">ปี (พ.ศ.) :</label>
                <div class="col-md-3">
                  <select class="form-control" name="stat_year">
                    @for($i = (date('Y')+543) ;$i >= (date('Y',strtotime("-3 year"))+543) ; $i--)
                      <option value="{{ $i }}" @if($i == $yy) selected @else '' @endif>{{ $i }}</option>
                    @endfor
                  </select>
                </div>
            </div>
            @if(Auth::user()->type == "5" || Auth::user()->type == "1" || Auth::user()->type == "4")
            <div class="form-group row">
              @if(Auth::user()->type == "5" || Auth::user()->type == "1")
              <label class="col-md-2 col-form-label">ชื่อฝ่าย (ย่อ) : <font color="red">*</font></label>
              <div class="form-group col-sm-4">
                <div class="input-group">
                  <input class="form-control @error('cost_title') is-invalid @enderror" type="text" name="cost_title">
                  @error('cost_title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              @endif
              <label class="col-md-2 col-form-label">รหัสศูนย์ต้นทุน :</label>
              <div class="form-group col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" name="center_money">
                </div>
              </div>
            </div>
            @endif
            <div class="form-group row">
              <div class="form-group col-sm-4">
                <div class="input-group">
                  <button type="submit" class="btn btn-primary">Submit</button>
          </form>
                  <form action="{{ route('print_view') }}" method="post">
                    @csrf
                    @if(Auth::user()->type == "5" || Auth::user()->type == "1" || Auth::user()->type == "5" || Auth::user()->type == "4")
                      <input type="hidden" name="center_money" value="{{ $fundcenter }}">
                      <input type="hidden" name="cost_title" value="{{ $cost }}">
                    @endif
                    <input type="hidden" name="year" value="{{ $yy }}">
                    &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Export</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </div>
          @csrf
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์เงินทุน</th>
                <th>ศูนย์ต้นทุน</th>
                <th>ฝ่าย</th>
                <th>หมวดค่าใช้จ่าย</th>
                <th>รายการภาระผูกพัน</th>
                <th>งบประมาณ</th>
                <th>สถานะ</th>
              </tr>
            </thead>
            <tbody>
            @if(isset($views))
              @php
                $sum =0;
              @endphp
              @foreach($views as $key => $arr_value)
                @foreach($arr_value as $value)
                @php
                  $sum += $value['budget'];
                @endphp
                <tr>
                  <td align="center">{{$value['stat_year']}}</td>
                  <td align="center">{{$value['fund_center']}}</td>
                  <td align="center">{{$value['center_money']}}</td>
                  <td align="center">{{$value['cost_title']}}</td>
                  <td align="center">{{$value['account']}}</td>
                  <td>{{Func::get_account($value['account'])}}</td>
                  <td align="right">{{number_format($value['budget'],2)}}</td>
                  {{--<input type="hidden" name="year[]" value="{{date('Y')+543}}">
                  <input type="hidden" name="center_money[]" value="{{$value['center_money']}}">--}}
                  @if($value['status'] == "0")
                    <td align="center"><span class="badge badge-pill badge-warning">ฝ่าย/เขต อนุมัติแล้ว</span></td>
                  @elseif($value['status'] == "1")
                    <td align="center"><span class="badge badge-pill badge-success">งบประมาณอนุมัติแล้ว</span></td>
                  @elseif($value['status'] == 5)
                    <td align="center"><span class="badge badge-pill badge-danger">งบประมาณรอพิจารณา</span></td>
                  @elseif($value['status'] == "4")
                    <td align="center"><span class="badge badge-pill badge-danger">แก้ไขงบประมาณ</span></td>
                  @elseif($value['status'] == "3")
                    <td align="center"><span class="badge badge-pill badge-warning">วง.ขอแก้ไขงบ</span></td>
                  @endif
                </tr>
                @endforeach
              @endforeach
            </tbody>
            <tr>
              <td colspan="6" align="right"><b>Sum</b></td>
              <td align="right"><b>{{ number_format($sum,2) }}</b></td>
              <td></td>
            </tr>
          </table>
          @endif

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

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <!-- <script src="{{ asset('admin/js/i18n/defaults-*.min.js') }}"></script> -->


@endsection
