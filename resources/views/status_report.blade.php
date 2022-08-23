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
        <a href="{{ route('dashboard') }}">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">ขั้นตอนงบประมาณทำการ</li>
    </ol>
    <!-- end breadcrumb -->
    <div class="card-body">
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          @if(Auth::user()->type == 5 || Auth::user()->type == 6 || Auth::user()->type == 1)
          <form action="{{ route('post_status') }}" method="post">
              @csrf
              <div class="row">
                <div class="form-group col-sm-3">
                  <label for="postal-code">สายงาน : </label>
                    <select class="form-control div_id" name="div_id" id="div_id">
                      <option value="">--เลือกสายงาน--</option>
                    @foreach($str as $val)
                      <option value="{{ $val->FundsCenterID }}" @if($divid == $val->FundsCenterID) selected @else '' @endif>{{ Func::get_name_costcenter_by_divID($val->FundsCenterID) }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                  <input type="submit" class="btn btn-primary" value="Submit" style="margin-top: 20px;">
                </div>
              </div>
          </form>
          @endif
          @if(isset($status))
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
                <th>วง.1 อนุมัติ</th>
              </tr>
            </thead>
            <tbody>
                @foreach($status as $key => $value)
                  <tr>
                    <td align="center">{{ $value["stat_year"] }}</td>
                    <td>{{ Func::FundID_name($value["fund_center"]) }}</td>
                    <td align="center">{{ $value["center_money"] }}</td>
                    <td align="center">{{ $value["cost_title"] }}</td>
                    <td align="right">{{ number_format($value["budget"],2) }}</td>
                    @if($value["status"] == "6")
                      <td align="center">{{ '-' }}</td>
                      <td align="center">{{ '-' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "5")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">{{ '-' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "3")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">{{ 'วง.1 ขอแก้ไขงบ' }}</td>
                      <td align="center">{{ '-' }}</td>
                    @elseif($value["status"] == "1")
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                      <td align="center">กำลังพิจารณา</td>
                    @elseif($value["status"] == "2")
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
      });
  </script>

@endsection
