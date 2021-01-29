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
        <form class="" action="{{ route('insert_est') }}" method="post">
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
                      <input class="form-control @error('stat_year') is-invalid @enderror" type="text" name="stat_year" value="{{ date('Y')+543 }}" readonly>

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
                    <input class="form-control @error('phone') is-invalid @enderror" type="text" name="phone" value="{{\Auth::user()->tel}}" placeholder="ตัวเลขเท่านั้น">
                    @error('phone')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
          </div>
         </div>
           <table class="table table-responsive-sm table-bordered" id="myTable" style="width: 150%">
             <thead>
               <tr>
                 <th>รหัสบัญชี</th>
                 <th>หมวด/ประเภทรายจ่าย</th>
                 <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-3 year"))+543)}}</th>
                 <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-2 year"))+543)}}</th>
                 <th>{{'ประมาณจ่ายปี '.(date("Y",strtotime("-1 year"))+543)}}</th>
                 <th>{{'งบประมาณขอตั้งปี '.(date("Y")+543)}}</th>
                 <th>{{'% ผลรวมที่เพิ่มขึ้นจากปี '.(date("Y",strtotime("-1 year"))+543)}}</th>
                 <th>คำอธิบาย</th>
               </tr>
             </thead>
             <tbody>
              @foreach($status as $key =>$arr_year)
                @foreach($arr_year as $account => $value)
                <tr>
                  <td>{{ $account }}</td>
                  <td>{{ Func::get_account($account) }}</td>
                  @if(isset($year3[date("Y")+540][$account]))
                    <td align="right">{{ number_format($year3[date("Y")+540][$account],2) }}</td>
                  @else
                    <td align="center">{{ '-' }}</td>
                  @endif
                  @if(isset($year2[date("Y")+541][$account]))
                    <td align="right">{{ number_format($year2[date("Y")+541][$account],2) }}</td>
                  @else
                    <td align="center">{{ '-' }}</td>
                  @endif
                  @if(isset($year1[date("Y")+542][$account]))
                    <td align="right">{{ number_format($year1[date("Y")+542][$account],2) }}</td>
                  @else
                    <td align="center">{{ '-' }}</td>
                  @endif
                  @if($status[$key][$account] >= 3)
                  <td align="center">
                    <input class="form-control" type="text" name="budget[{{$account}}]" value="{{ $now[$key][$account] }}">
                  </td>
                  @else
                  <td align="center">
                    <input class="form-control" type="text" name="budget[{{$account}}]" value="{{ $now[$key][$account] }}" readonly>
                  </td>
                  @endif
                  @if($now[date("Y")+543][$account] != 0 && isset($year1[date("Y")+542][$account]) && $year1[date("Y")+542][$account] < $now[date("Y")+543][$account])
                    @php
                      $cal = (($now[date('Y')+543][$account] - $year1[date("Y")+542][$account]) * 100 / $year1[date("Y")+542][$account]);
                    @endphp
                    <td align="center">{{round($cal,2).' %'}}</td>
                  @else
                    <td align="center">{{ '-' }}</td>
                  @endif
                  <td align="center">
                    <input class="form-control" type="text" name="reason[{{$account}}]" value="{{ $reason[$key][$account] }}">
                  </td>
                </tr>
                @endforeach
              @endforeach
              @foreach($test as $id)
              <tr>
                <td>{{ $id->account }}</td>
                <td>{{ Func::get_account($id->account) }}</td>
                @if(isset($year3[date("Y")+540][$id->account]))
                  <td align="right">{{ number_format($year3[date("Y")+540][$id->account],2) }}</td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
                @if(isset($year2[date("Y")+541][$id->account]))
                  <td align="right">{{ number_format($year2[date("Y")+541][$id->account],2) }}</td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
                @if(isset($year1[date("Y")+542][$id->account]))
                  <td align="right">{{ number_format($year1[date("Y")+542][$id->account],2) }}</td>
                @else
                  <td align="center">{{ '-' }}</td>
                @endif
                <td align="center">
                  <input class="form-control" type="text" name="budget[{{$id->account}}]">
                </td>
                <td align="center">{{ '-' }}</td>
                <td align="center">
                  <input class="form-control" type="text" name="reason[{{$id->account}}]">
                </td>
              </tr>
              @endforeach
             </tbody>
           </table>
           <div class="form-group row">
             <div class="col-md-2 form-group">
               <button class="btn btn-primary" type="submit">Submit</button>
             </div>
           </div>
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
<script type="text/javascript">
$(document).ready(function() {
  $('#myTable').DataTable({
    "paging":   false,
    "ordering": false,
    "autoWidth": false,
    scrollX:true
  });

});
</script>
@endsection
