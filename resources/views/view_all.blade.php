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
      <li class="breadcrumb-item">
        <a href="#">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">ข้อมูลงบประมาณทำการประจำปี</li>
    </ol>
    <!-- end breadcrumb -->
<div class="card-body">
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-block">
     <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          @if(Auth::user()->type == "5" || Auth::user()->type == "1")
            <form action="{{ route('post_view') }}" method="post">
              <div class="form-group row">
              @csrf
              <label class="col-md-2 col-form-label" for="date-input">ชื่อฝ่าย(ย่อ) : <font color="red">*</font></label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input class="form-control @error('fund_center') is-invalid @enderror" type="text" name="cost_title" value="{{ old('cost_title') }}">
                    @error('fund_center')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          @endif
            {{--<form action="{{ route('print_view') }}" method="post">
              @csrf
              <input type="hidden" name="year" value="{{ date('Y') }}">
              <input type="hidden" name="center_money" value="{{ $center }}">
              &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Export</button>
            </form>--}}

        <form action="{{route('post_approve')}}" method="post">
          @csrf
          <table class="table table-responsive-sm table-bordered myTable" style="width: 150%">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์เงินทุน</th>
                <th>ศูนยต้นทุน</th>
                <th>ฝ่าย</th>
                <th>ส่วน</th>
                <th>หมวดค่าใช้จ่าย</th>
                <th>รายการภาระผูกพัน</th>
                <th>งบประมาณ</th>
                @if(Auth::user()->type == 4 || Auth::user()->type == 1)
                <th>งบประมาณใหม่</th>
                <th>ฝ่าย/เขต</th>
                @endif
                @if(Auth::user()->type == 5 || Auth::user()->type == 1)
                <th>งบประมาณใหม่</th>
                <th>วง.</th>
                @endif
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
                  unset($center_money1);
                @endphp
                <tr>
                  <td align="center">{{$value['stat_year']}}</td>
                  <td align="center">{{$value['fund_center']}}</td>
                  <td align="center">{{$value['center_money']}}</td>
                  <td align="center">{{$value['cost_title']}}</td>
                  <td>{{ Func::get_name_costcenter($value['center_money']) }}</td>
                  <td align="center">{{$value['account']}}</td>
                  <td>{{Func::get_account($value['account'])}}</td>
                  <td align="right">{{number_format($value['budget'],2)}}</td>
                  @if(Auth::user()->type == 4 || Auth::user()->type == 1)
                  <?php
                    $able = '';
                    if($value['status'] == "1"){
                      $able = 'disabled';
                    }
                   ?>
                   <td align="center">
                     <input class="form-control" type="text" name="new1[{{$value['account']}}][{{$value['center_money']}}]" value="{{$value['budget']}}" @if($able == 'disabled') readonly @endif>
                   </td>
                    <td align="center"><input type="checkbox" name="approve1[]" value="{{$value['account'].'-'.$value['center_money']}}"<?php echo $able; ?>></td>
                  @endif

                  @if(Auth::user()->type == 5 || Auth::user()->type == 1)
                  <?php
                    $able = 'disabled';
                    if($value['status'] == "0" || $value['status'] == "1"){
                      $able = '';
                    }
                   ?>
                    <td align="center">
                      <input class="form-control" type="text" name="new2[{{$value['account']}}][{{$value['center_money']}}]" value="{{$value['budget']}}" @if($able == 'disabled') readonly @endif>
                    </td>
                    <td align="center"><input type="checkbox" name="approve2[]" value="{{$value['account'].'-'.$value['center_money']}}" <?php echo $able; ?>></td>

                  @endif
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
              <td colspan="7" align="right"><b>Sum</b></td>
              <td align="right"><b>{{ number_format($sum,2) }}</b></td>
              @if(Auth::user()->type == 4 || Auth::user()->type == 1)
              <td></td>
                <td>Select All <input type="checkbox" id="all1" onclick='selectAll1()'></td>
              @endif
              @if(Auth::user()->type == 5 || Auth::user()->type == 1)
              <td></td>
                <td>Select All <input type="checkbox" id="all2" onclick='selectAll2()'></td>
              @endif
              <td></td>
            </tr>
          </table>
          @endif
          @if((Auth::user()->type == 5 || Auth::user()->type == 4 || Auth::user()->type == 1) && !empty($views))
          <button type="submit" class="btn btn-success" name="btn" value="true">
            <i class="nav-icon fa fa-check"></i> Approve log
          </button>
          <button type="submit" class="btn btn-danger" name="btn" value="false">
            <i class="nav-icon fa fa-times"></i> Unapprove log
          </button>
          @endif
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
    <!-- Latest compiled and minified JavaScript -->
  <script src="{{ asset('admin/js/bootstrap-select.min.js')}}"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <!-- <script src="{{ asset('admin/js/i18n/defaults-*.min.js') }}"></script> -->

  <script type="text/javascript">
      $('.myTable').DataTable({
        select:true,
        scrollX:true,
        "paging":false,
        "autoWidth": false,
        order:[[ 2, "asc" ]]
      });
      function selectAll1() {
        if($('#all1').is(':checked') == true){
          var items = document.getElementsByName('approve1[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = true;
          }
        }else{
          var items = document.getElementsByName('approve1[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = false;
          }
        }
      }
      function selectAll2() {
        // console.log($('#all1').is(':checked'));
        if($('#all2').is(':checked') == true){
          var items = document.getElementsByName('approve2[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = true;
          }
        }else{
          var items = document.getElementsByName('approve2[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = false;
          }
        }
      }

  </script>


@endsection
