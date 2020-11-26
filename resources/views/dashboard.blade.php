@extends('layout')

@section('title')
<title>Dashboard page</title>
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
    </ol>
    <!-- end breadcrumb -->

<div class="card-body">
  <div class="container-fluid">
    @if(Auth::user()->type == 5 || Auth::user()->type == 1)
    <button class="btn btn-info mb-1" type="button" data-toggle="modal" data-target="#myModal">+ เพิ่มข่าวสาร</button>
    <table class="table table-responsive-sm table-bordered">
      <tr>
        <th>ลำดับ</th>
        <th>ชื่อประกาศ</th>
        <th>ลบ</th>
      </tr>
      @foreach($data as $value)
      <tr>
        <td align="center">{{ $loop->iteration }}</td>
        <td>{{ $value->name }}</td>
        <td align="center">
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="{{'#myDelete'.$value->id}}">
            <i class="nav-icon icon-trash"></i> Delete
          </button>
        </td>
      </tr>
      <div class="modal fade" id="{{'myDelete'.$value->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-danger" role="document">
          <div class="modal-content">
            <form action="{{ route('delete_infor') }}" method="POST">
              @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">ลบข้อมูลโครงสร้าง</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
             <div class="modal-body">
               <p>ต้องการลบข้อมูลโครงสร้างนี้หรือไม่?</p>
               <input type="hidden" name="id" value="{{ $value->id }}">
             </div>
             <div class="modal-footer">
               <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
               <button class="btn btn-danger" type="submit">Delete</button>
             </div>
             </form>
           </div>

         </div>
       </div>
      @endforeach
    </table>
    @endif
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
          <div class="card-header word">
            <i class="fa fa-align-justify"></i> ข่าวสารประกาศจาก วง.</div>
            <div class="card-body">
              @foreach($data as $value)
              <a href="{{ url('/open/'.$value->path_file) }}">{{ $value->name }}</a>
              <p></p>
              @endforeach
          </div>
         </div>
       </div>
     </div>
   </div>
   @if(Auth::user()->type == 2 ||Auth::user()->type == 3)
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          สถานะอนุมัติงบประมาณ
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์ต้นทุน</th>
                <th>งบประมาณ</th>
                <th>ตั้งงบ</th>
                <th>ฝ่าย/เขต อนุมัติ</th>
                <th>วง. อนุมัติ</th>
              </tr>
            </thead>
            <tbody>
              @if($status != NULL)
              @foreach($status as $key => $value)
              <tr>
                <td align="center">{{ $value["stat_year"] }}</td>
                <td align="center">{{ $value["center_money"] }}</td>
                <td align="right">{{ number_format($value["budget"],2) }}</td>
                @if($value["status"] == "5")
                  <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                  <td align="center">{{ '-' }}</td>
                  <td align="center">{{ '-' }}</td>
                @elseif($value["status"] == "3")
                  <td align="center"><i class="nav-icon fa fa-check" style="color:green;"></i></td>
                  <td align="center">{{ 'วง.ขอแก้ไขงบ' }}</td>
                  <td align="center">{{ '-' }}</td>
                @elseif($value["status"] == "1")
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
  @endif
  @if(Auth::user()->type == 2 ||Auth::user()->type == 3 || Auth::user()->type == 4)
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-lg-12">
        สถานะงบประมาณ(ภาพรวม)
        <table class="table table-responsive-sm table-bordered myTable">
          <thead>
            <tr>
              <th>ปีงบประมาณ</th>
              <th>ตั้งงบประมาณ</th>
              <th>เขต/ฝ่าย อนุมัติ</th>
              <th>วง.อนุมัติ</th>
              <th>ปรับแก้งบประมาณ</th>
              <th>วง.ขอแก้งบประมาณ</th>
            </tr>
          </thead>
          <tbody>
            @if(count($stat) > 0)
            <tr>
              <td align="center">{{ date('Y')+543 }}</td>
              <td align="right">{{ number_format($stat["5"],2) }}</td>
              <td align="right">{{ number_format($stat["0"],2) }}</td>
              <td align="right">{{ number_format($stat["1"],2) }}</td>
              <td align="right">{{ number_format($stat["4"],2) }}</td>
              <td align="right">{{ number_format($stat["3"],2) }}</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
  </div>
</div>
</main>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
       <h4 class="modal-title">เพิ่มประกาศ</h4>
       <button class="close" type="button" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">×</span>
       </button>
      </div>
      <form action="{{ route('inform') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-md-4 col-form-label" for="email-input">ชื่อประกาศ</label>
            <div class="col-md-6">
              <input class="form-control" type="text" name="name">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label" for="date-input">Select File</label>
            <div class="col-md-6">
              <input id="file-input" type="file" name="select_file"><span class="text-muted">.pdf</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
   <!-- /.modal-content-->
  </div>
 <!-- /.modal-dialog-->
</div>
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
