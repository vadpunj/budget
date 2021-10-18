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
      <li class="breadcrumb-item active">อนุมัติงบประมาณทำการประจำปี</li>
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
          {{--@if(Auth::user()->type == "5" || Auth::user()->type == "1")--}}
            <form action="{{ route('post_view_approve') }}" method="post">
              @csrf
                <div class="row">
                  <div class="form-group col-sm-3">
                    <label for="city">หมวดหมู่ ค/จ : </label>
                    <select class="form-control" name="id1" id="id1">
                      @foreach($cmmt as $value)
                        <option value="{{ $value->name_id }}" @if($cat_cm == $value->name_id) selected @else '' @endif>{{ $value->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  @if(Auth::user()->type == 5 || Auth::user()->type == 1 || Auth::user()->type == 6)
                  <div class="form-group col-sm-3">
                    <label for="postal-code">สายงาน : </label>
                      <select class="form-control div_id" name="div_id" id="div_id">
                        <option value="">--เลือกสายงาน--</option>
                      @foreach($str as $val)
                        <option value="{{ $val->FundsCenterID }}" @if($divid == $val->FundsCenterID) selected @else '' @endif>{{ Func::get_name_costcenter_by_divID($val->FundsCenterID) }}</option>
                      @endforeach
                      </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label for="postal-code">ฝ่าย : </label>
                      <select class="form-control fund_id" name="fund_id" id="fund_id">
                        <option value="0">--กรุณาเลือกสายงานก่อน--</option>
                      </select>
                  </div>
                  @endif
                  <div class="col-md-4">
                    <input type="submit" class="btn btn-primary" value="Submit" style="margin-top: 20px;">
                  </div>
                </div>
            </form><br>
            <form action="{{route('approve_log')}}" method="post">
            @if(!is_null($bg))
              @csrf
              <table class="table table-responsive-sm table-bordered" style="width: 100%">
                <thead>
                  <tr>
                    <th>หมวดค่าใช้จ่าย</th>
                    <th>รายการภาระผูกพัน</th>
                    <th>งบประมาณ</th>
                    @if(Auth::user()->type == 5)
                    <th>งบประมาณใหม่</th>
                    <th>วง.1</th>
                    @endif
                    @if(Auth::user()->type == 6)
                    <th>งบประมาณใหม่</th>
                    <th>ผสทก</th>
                    @endif
                    <th>สถานะ</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $sum =0;
                  @endphp
                  @foreach($bg as $id2 => $arr_acc)
                    <tr>
                      @if($cat_cm == 1 && $id2 == 1)
                        <td colspan="4">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</td>
                      @elseif($cat_cm == 1 && $id2 == 2)
                        <td colspan="4">1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</td>
                      @endif
                      @if($cat_cm == 2 && $id2 == 1)
                        <td colspan="4">2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง</td>
                      @elseif($cat_cm == 2 && $id2 == 2)
                        <td colspan="4">2.2 ค่าสวัสดิการผู้บริหาร</td>
                      @endif
                    </tr>
                    @foreach($arr_acc as $acc => $value)
                      @php
                        $sum += $value;
                      @endphp
                    <tr>
                      <td>{{ Func::get_account($acc) }}</td>
                      <td align="center">{{ $acc }}</td>
                      <td align="right">{{ number_format($value,2) }}</td>
                      @if(Auth::user()->type == 5)
                      <?php
                        $able = '';
                        if($status[$id2][$acc] == "1" || $status[$id2][$acc] == "0"){
                          $able = 'disabled';
                        }
                       ?>
                      <td align="center">
                        <input class="form-control" type="text" name="new1[{{$acc}}][{{$val->FundsCenterID}}]" value="{{$value}}" @if($able == 'disabled') readonly @endif>
                      </td>
                       <td align="center"><input type="checkbox" name="approve2[]" value="{{$acc.'-'.$val->FundsCenterID}}"<?php echo $able; ?>></td>
                      @endif
                      <input type="hidden" name="id2" value="{{ $id2 }}">
                      @if(Auth::user()->type == 6)
                      <?php
                        $able = 'disabled';
                        if($status[$id2][$acc] == "0" || $status[$id2][$acc] == "1"){
                          $able = '';
                        }
                       ?>
                        <td align="center">
                          <input class="form-control" type="text" name="new2[{{$acc}}][{{$val->FundsCenterID}}]" value="{{$value}}" @if($able == 'disabled') readonly @endif>
                        </td>
                        <td align="center"><input type="checkbox" name="approve_all[]" value="{{$acc.'-'.$val->FundsCenterID}}" <?php echo $able; ?>></td>
                      @endif
                      @if($status[$id2][$acc] == "0")
                        <td align="center"><span class="badge badge-pill badge-warning">วง.1อนุมัติแล้ว</span></td>
                      @elseif($status[$id2][$acc] == "1")
                        <td align="center"><span class="badge badge-pill badge-success">งบประมาณอนุมัติแล้ว</span></td>
                      @elseif($status[$id2][$acc] == 5)
                        <td align="center"><span class="badge badge-pill badge-danger">รอวง.1อนุมัติ</span></td>
                      @elseif($status[$id2][$acc] == "4")
                        <td align="center"><span class="badge badge-pill badge-danger">แก้ไขงบประมาณ</span></td>
                      @elseif($status[$id2][$acc] == "3")
                        <td align="center"><span class="badge badge-pill badge-warning">วง.ขอแก้ไขงบ</span></td>
                      @endif
                    </tr>
                      @endforeach
                  @endforeach
                  </tbody>
                  <tr>
                    <td colspan="2" align="right"><b>Sum</b></td>
                    <td align="right"><b>{{ number_format($sum,2) }}</b></td>
                    @if(Auth::user()->type == 5)
                    <td></td>
                      <td>Select All <input type="checkbox" id="all1" onclick='selectAll1()'></td>
                    @endif
                    @if(Auth::user()->type == 6)
                    <td></td>
                      <td>Select All <input type="checkbox" id="all2" onclick='selectAll2()'></td>
                    @endif
                    <td></td>
                  </tr>
                </table>
              @endif
              @if((Auth::user()->type == 5 || Auth::user()->type == 6) && !empty($bg))
              <button type="submit" class="btn btn-success" name="btn" value="true">
                <i class="nav-icon fa fa-check"></i> Approve log
              </button>
              <input type="hidden" name="div_id" value="{{ $divid }}">
              <input type="hidden" name="fund_id" value="{{ $fundid }}">
              <input type="hidden" name="id1" value="{{ $cat_cm }}">
              {{--<button type="submit" class="btn btn-danger" name="btn" value="false">
                <i class="nav-icon fa fa-times"></i> Unapprove log
              </button>--}}
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
    $(document).ready(function() {
      $(document).on('click','.div_id',function(){
        // console.log("its change");
        var cat_id=$(this).val();
        // var divid=document.getElementById("fun_id").value;
        // console.log(divid);
        var div=$(this).parents();
        var op=" ";
        // console.log(cat_id);
        $.ajax({
          type: 'get',
          url:"{{ route('change_id') }}",
          data:{'id': cat_id},
          success:function(data){
            // console.log('success');
            // console.log(data.length);
            // console.log(data.length);
            op+='<option selected="selected" value="0">--กรุณาเลือกสายงานก่อน--</option>'
            for(var i=0;i<data.length;i++){
              // console.log(data[i]["CostCenterName"]);
              op+='<option value="'+data[i]["FundID"]+'">'+data[i]["CostCenterName"]+'</option>'
            }
            div.find('.fund_id').empty();
            div.find('.fund_id').append(op);
          }

        })
      });
    });
    // function submit() {
    //   document.getElementById("mydata").submit();
    // }
  </script>
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
      function selectAll2() {
        // console.log($('#all1').is(':checked'));
        if($('#all2').is(':checked') == true){
          var items = document.getElementsByName('approve_all[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = true;
          }
        }else{
          var items = document.getElementsByName('approve_all[]');
          for (var i = 0; i < items.length; i++) {
              if (items[i].type == 'checkbox')
                  items[i].checked = false;
          }
        }
      }

  </script>


@endsection
