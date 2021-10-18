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
        <div class="card-header">
          <i class="fa fa-align-justify"></i> เปรียบเทียบงบประมาณ</div>
          <div class="card-body">
            <form action="{{ route('post_compare') }}" method="post">
                @csrf
              <div class="form-group row">
                @if(Auth::user()->type == "5" || Auth::user()->type == 6 ||Auth::user()->type == "1")
                <label class="col-md-2 col-form-label">สายงาน : <font color="red">*</font></label>
                  <div class="col-md-4">
                    <select class="form-control div_id" name="div_id" id="div_id">
                      <option value="">--เลือกสายงาน--</option>
                    @foreach($str as $val)
                      <option value="{{ $val->FundsCenterID }}" @if($divid == $val->FundsCenterID) selected @else '' @endif>{{ Func::get_name_costcenter_by_divID($val->FundsCenterID) }}</option>
                    @endforeach
                    </select>
                  </div>
                  <label class="col-md-1 col-form-label">ฝ่าย : <font color="red">*</font></label>
                    <div class="col-md-4">
                      <select class="form-control fund_id" name="fund_id" id="fund_id">
                        <option value="0">--กรุณาเลือกสายงานก่อน--</option>
                        <option value="all">ทั้งหมด</option>
                      </select>
                    </div>
                @endif
              </div>
              <div class="form-group row">
                <label class="col-md-2 col-form-label">หมวดค่าใช้จ่าย<font color="red">*</font></label>
                <div class="col-md-4">
                  <select class="form-control" name="id1">
                    @foreach($cmmt as $value)
                    <option value="{{ $value->name_id }}" @if($id1 == $value->name_id) selected @else '' @endif>{{ $value->name }}</option>
                    @endforeach
                  </select>
                </div>
                <label class="col-md-2 col-form-label">ปี (พ.ศ.) :<font color="red">*</font></label>
                  <div class="col-md-3">
                    <select class="form-control" name="stat_year">
                      @for($i = (date('Y')+544) ;$i >= (date('Y',strtotime("-3 year"))+544) ; $i--)
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
                        <input type="hidden" name="fundcenter" value="{{ $fund_id }}">
                        <input type="hidden" name="statyear" value="{{ $yy }}">
                        <input type="hidden" name="account" value="{{ $id1 }}">
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
        <th>ชื่อ</th>
        @if(Auth::user()->type == 5|| Auth::user()->type == 6)
        <th>ฝ่าย</th>
        @else
        <th>ส่วน</th>
        @endif
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
            <td>{{ Func::get_account($key_acc) }}</td>
            @if(Auth::user()->type == 2 ||Auth::user()->type == 3)
              <td align="center">{{ Func::get_cost_title($fund_id) }}</td>
            @else
              <td>{{ Func::FundID_name($fund_id) }}</td>
            @endif
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
          <td align="right" colspan="3"><b>Sum</b></td>
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
  $(document).ready(function() {
    $('#myTable').DataTable({
      scrollX:true
    });
  });

  </script>
@endsection
