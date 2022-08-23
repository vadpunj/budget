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
    .tableFixHead          { overflow: auto; height: 700px; }
    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 8px 16px; }
    th     { background:#eee; }
  </style>
@endsection

@section('content')
<main class="main">
  <!-- Breadcrumb-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="{{ route('dashboard') }}">หน้าแรก</a>
    </li>
    <li class="breadcrumb-item active">ภาพรวมงบประมาณ</li>
  </ol>
  <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> ภาพรวมงบประมาณ</div>
          <div class="card-body">
            <form action="{{ route('post_view_estimate') }}" method="post">
                @csrf
                <div class="row">
                  @if(Auth::user()->type == 5 || Auth::user()->type == 1)
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
                  <div class="form-group col-sm-3">
                    <label for="postal-code">ส่วน : </label>
                      <select class="form-control center_id" name="center_id" id="center_id">
                        <option value="0">--กรุณาเลือกฝ่ายก่อน--</option>
                        <option value="all">ทั้งหมด</option>
                      </select>
                  </div>
                  @endif
                  @if(Auth::user()->type == 4)
                  <div class="form-group col-sm-5">
                    <label for="postal-code">ส่วน : </label>
                      <select class="form-control" name="center_id">
                        @foreach($fund_cen as $data)
                          <option value="{{ $data->CostCenterID }}" @if($fun_id == $data->CostCenterID) selected @else '' @endif>{{ $data->CostCenterName }}</option>
                        @endforeach
                      </select>
                  </div>
                  @endif
                  <div class="col-md-4">
                    <input type="submit" class="btn btn-primary" value="Submit" style="margin-top: 20px;">
                  </div>
                </div>
                @if(Auth::user()->type == 5)
                  <font color="red">*หากไม่มีชื่อฝ่ายขึ้น ให้กดเลือกสายงานอีกครั้ง*</font>
                @endif
            </form>
          </div>
      </div>
    </div>
    @if(isset($name))
    <div class="tableFixHead">
      <table class="table table-responsive-sm table-bordered" >
        <thead>
          <tr>
            <th>รหัสบัญชี</th>
            <th>หมวด/ประเภทรายจ่าย</th>
            <th>{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-3 year"))+544)}}</th>
            <th>{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-2 year"))+544)}}</th>
            <th>{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-1 year"))+544)}}</th>
            <th>{{'งบประมาณขอตั้งปี '.(date("Y")+544)}}</th>
            @if($type != 'all')
            <th>{{'คำอธิบาย'}}</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @php
            $all_sum = 0;
            $all_sum1 = 0;
            $all_sum2 = 0;
            $all_sum3 = 0;
          @endphp
         @foreach($name as $id1 => $arr_id2)
         <tr>
             <td  colspan="8"><b>{{ $head[$id1] }}</b></td>
         </tr>
         @php
           $sum = 0;
           $sum1 = 0;
           $sum2 = 0;
           $sum3 = 0;
         @endphp
           @foreach($arr_id2 as $id2 => $arr_year)
           <tr>
               @if($id1 == 1 && $id2 == '1')
                 <td  colspan="8">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</td>
               @elseif($id1 == 1 && $id2 == '2')
                 <td  colspan="8">1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</td>
               @endif
               @if($id1 == 2 && $id2 == '1')
                 <td  colspan="8">2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง</td>
               @elseif($id1 == 2 && $id2 == '2')
                 <td  colspan="8">2.2 ค่าสวัสดิการผู้บริหาร</td>
               @endif
           </tr>
           @foreach($arr_year as $year =>$arr_acc)
             @foreach($arr_acc as $account => $value)
           <tr>
             <td>{{ $account }}</td>
             <td>{{ Func::get_account($account) }}</td>
             @if(!empty($year3[date("Y")+541][$account]))
             @php
               $sum3 += $year3[date("Y")+541][$account];
               $all_sum3 += $year3[date("Y")+541][$account];
             @endphp
               <td align="right">{{ number_format($year3[date("Y")+541][$account],2) }}</td>
             @else
               <td align="center">{{ '-' }}</td>
             @endif
             @if(!empty($year2[date("Y")+542][$account]))
             @php
               $sum2 += $year2[date("Y")+542][$account];
               $all_sum2 += $year2[date("Y")+542][$account];
             @endphp
               <td align="right">{{ number_format($year2[date("Y")+542][$account],2) }}</td>
             @else
               <td align="center">{{ '-' }}</td>
             @endif
             @if(!empty($year1[date("Y")+543][$account]))
             @php
               $sum1 += $year1[date("Y")+543][$account];
               $all_sum1  += $year1[date("Y")+543][$account];
             @endphp
               <td align="right">{{ number_format($year1[date("Y")+543][$account],2) }}</td>
             @else
               <td align="center">{{ '-' }}</td>
             @endif
             @if(!empty($now[date("Y")+544][$account]))
             @php
               $sum += $now[date("Y")+544][$account];
               $all_sum += $now[date("Y")+544][$account];
             @endphp
               <td align="right">{{ number_format($now[$year][$account],2) }}</td>
             @else
               <td align="center">{{ '-' }}</td>
             @endif
             @if($type != 'all')
               @if(!empty($reason[date("Y")+544][$account]))
                 <td>{{ $reason[date("Y")+544][$account] }}</td>
               @else
                 <td align="center">{{ '-' }}</td>
               @endif
             @endif

           </tr>
               @endforeach
             @endforeach
           @endforeach
           <tr>
             <td align="center" colspan="2"><b>Sum</b></td>
             <td align="right"><b>{{ number_format($sum3,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum2,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum1,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum,2) }}</b></td>
           </tr>
         @endforeach
         <tr>
           <td align="center" colspan="2"><b>Sum Total</b></td>
           <td align="right"><b>{{ number_format($all_sum3,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum2,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum1,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum,2) }}</b></td>
         </tr>

        </tbody>
      </table>
    </div>
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
        var cat_id=$(this).val();
        // console.log(divid);
        var div=$(this).parents();
        var op=" ";
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
      $(document).on('click','.fund_id',function(){
        // console.log("its change");
        var fun_id=$(this).val();
        // var divid=document.getElementById("fun_id").value;
        var fund=$(this).parents();
        var op=" ";
        // console.log(cat_id);
        $.ajax({
          type: 'get',
          url:"{{ route('change_fund') }}",
          data:{'id': fun_id},
          success:function(data){
            // console.log('success');
            // console.log(data.length);
            // console.log(data.length);
            op+='<option selected="selected" value="0">--กรุณาเลือกฝ่ายก่อน--</option>'
            op+='<option selected="selected" value="all">ทั้งหมด</option>'
            for(var i=0;i<data.length;i++){
              // console.log(data[i]["CostCenterName"]);
              op+='<option value="'+data[i]["CostCenterID"]+'">'+data[i]["CostCenterName"]+'</option>'
            }
            fund.find('.center_id').empty();
            fund.find('.center_id').append(op);
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
