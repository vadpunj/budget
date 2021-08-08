@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Add page</title>
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
        <a href="#">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">เพิ่มข้อมูลงบประมาณทำการ</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
     <div class="row">
      <div class="col-lg-12">

          @if($message = Session::get('success'))
          <div class="alert alert-success alert-block">
           <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
          </div>
          @endif
          <div class="card">
          <div class="card-header word">
            <i class="fa fa-align-justify"></i> ข้อมูลผู้ขอ</div>
            <div class="card-body">
                @csrf
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ปีงบประมาณ(พ.ศ.)</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control @error('stat_year') is-invalid @enderror" type="text" name="stat_year" value="{{ date('Y')+544 }}" readonly>

                    </div>
                  </div>
                  <label class="col-md-2 col-form-label">สายงาน</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control" type="text" name="field" value="{{\Auth::user()->field}}" readonly>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ฝ่าย/สำนักงาน/ศูนย์</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control" type="text" name="office" value="{{\Auth::user()->office}}" readonly>
                  </div>
                  <label class="col-md-2 col-form-label">ส่วน/สค.</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control" type="text" name="part" value="{{\Auth::user()->part}}" readonly>
                  </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">ศูนยเงินทุน</label>
                    <div class="form-group col-sm-4">
                      <input class="form-control" type="text" name="center_money" value="{{\Auth::user()->center_money}}" readonly>
                    </div>
                  <label class="col-md-2 col-form-label">เบอร์ติดต่อ</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('phone') is-invalid @enderror" type="text" name="phone" value="{{\Auth::user()->tel}}" readonly>
                    @error('phone')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <font color="red">*กรุณาตรวจสอบข้อมูลผู้ขอก่อนตั้งงบประมาณ*</font>
                </div>
          </div>
         </div>

       @if(isset($name))
       <form action="{{ route('post_add') }}" method="post">
         @csrf
      <div class="tableFixHead">
       <table class="table table-responsive-sm table-bordered">
         <thead>
           <tr>
             <th>รหัสบัญชี</th>
             <th>หมวด/ประเภทรายจ่าย</th>
             <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-3 year"))+544)}}</th>
             <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-2 year"))+544)}}</th>
             <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-1 year"))+544)}}</th>
             <th>{{'งบประมาณขอตั้งปี '.(date("Y")+544)}}</th>
             <th>{{'% ผลรวมที่เพิ่มขึ้นจากปี '.(date("Y",strtotime("-1 year"))+544)}}</th>
             <th>คำอธิบาย</th>
           </tr>
         </thead>
         <tbody>
          @foreach($name as $id1 => $arr_id2)
          <tr>
              <td  colspan="8"><b>{{ $head[$id1] }}</b></td>
              @php
                $sum = 0;
                $sum1 = 0;
                $sum2 = 0;
                $sum3 = 0;
              @endphp
          </tr>
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
              @endphp
                <td align="right">{{ number_format($year3[date("Y")+541][$account],2) }}</td>
              @else
                <td align="center">{{ '-' }}</td>
              @endif
              @if(!empty($year2[date("Y")+542][$account]))
              @php
                $sum2 += $year2[date("Y")+542][$account];
              @endphp
                <td align="right">{{ number_format($year2[date("Y")+542][$account],2) }}</td>
              @else
                <td align="center">{{ '-' }}</td>
              @endif
              @if(!empty($year1[date("Y")+543][$account]))
              @php
                $sum1 += $year1[date("Y")+543][$account];
              @endphp
                <td align="right">{{ number_format($year1[date("Y")+543][$account],2) }}</td>
              @else
                <td align="center">{{ '-' }}</td>
              @endif
              @if($stage == 6)
              <td align="center">
                <input class="form-control{{ ' '.$id1 }}" type="text" name="budget[{{$account}}]" value="{{ $now[$year][$account] }}">
              </td>
              @else
              @if(!empty($now[$year][$account]))
              @php
                $sum += $now[$year][$account];
              @endphp
                <td align="right">{{ number_format($now[$year][$account],2) }}</td>
              @else
                <td align="center">{{ '-' }}</td>
              @endif
              @endif
              @if(($now[$year][$account] != 0 && $year1[date("Y")+543][$account] != 0) && $year1[date("Y")+543][$account] <= $now[date("Y")+544][$account])
                @php
                  $cal = (($now[date('Y')+544][$account] - $year1[date("Y")+543][$account]) * 100 / $year1[date("Y")+543][$account]);
                @endphp
                <td align="center">{{round($cal,2).' %'}}</td>
              @else
                <td align="center">{{ '-' }}</td>
              @endif
              @if($stage == 6)
                <td align="center">
                  <input class="form-control" type="text" name="reason[{{$account}}]" value="{{ $reason[$year][$account] }}">
                </td>
              @else
                <td>{{ $reason[$year][$account] }}</td>
              @endif
            </tr>
                @endforeach
              @endforeach
            @endforeach
            <tr>
              <td align="center" colspan="2"><b>Sum</b></td>
              <td align="right">{{ number_format($sum3,2) }}</td>
              <td align="right">{{ number_format($sum2,2) }}</td>
              <td align="right">{{ number_format($sum1,2) }}</td>
              @if($stage == 6)
              <td align="center">
                <input class="form-control" type="text" name="{{ $id1 }}">
              </td>
              @else
              <td align="right">{{ number_format($sum,2) }}</td>
              @endif
            </tr>
          @endforeach
         </tbody>
       </table>
     </div>
       <div class="col-md-2 form-group">
         <button class="btn btn-primary" type="button" data-toggle="modal" data-target="{{'#myAlert'}}" @if($stage == 6) @else disabled @endif>Submit</button>
       </div>
       @endif
       </div>
     </div>
     </div>
   </div>
 </main>

 <div class="modal fade" id="{{'myAlert'}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">Alert Box</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
        <div class="modal-body">
          <p>ต้องการบันทึกข้อมูลเพื่อรอแก้ไข หรือส่งข้อมูลเพื่อพิจารณางบในขั้นถัดไป <br>โปรดเลือก</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit" name="type" value="save"><i class="nav-icon fa fa-save"></i> Save</button>
          <button class="btn btn-success" name="type" type="submit" value="send"><i class="nav-icon fa fa-sign-out"></i> Send Data</button>
        </div>
        </form>
      </div>

    </div>
  </div>

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
  $(document).ready(function() {
    $('#myTable').DataTable({
      "paging":   false,
      "ordering": false,
      // "autoWidth": false,
      scrollX:true
    });
    $("input").click(function(e) {

      for(var i =1 ; i<=19 ;i++){
        var tt = 0;
        var ss;
        var totn_string = '.';
        var sum = 'input[name=';
        sum = sum.concat(i);
        sum=sum.concat(']')
        $(totn_string.concat(i)).each(function() {
          if($(this).val()===""){
            ss=0;
          }else{
            ss = parseInt($(this).val())
          }
          tt = tt + ss;
        })
        $(sum).val(tt);
      }
    })
  });


</script>
@endsection
