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
      <li class="breadcrumb-item active">ข้อมูลงบประมาณทำการประจำปี</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-block">
     <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          <form action="{{ route('post_view') }}" method="post">
            @csrf
          <div class="form-group row">
            <label class="col-md-2 col-form-label" for="date-input">Year : </label>
            <div class="col-md-2">
                <select class="form-control" name="year" onchange="myFunction()">
                  @for($i = (date('Y')+543) ;$i > (date('Y',strtotime("-5 year"))+543) ; $i--)
                  <option value="{{ $i }}" @if($i == $yy) selected @else '' @endif>{{ $i }}</option>
                  @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์เงินทุน</th>
                <th>รหัสบัญชี</th>
                <th>หมวด/ประเภทรายจ่าย</th>
                <th>งบประมาณ</th>
                <th>สถานะ</th>
              </tr>
            </thead>
            <tbody>
              @php
                $sum =0;
              @endphp

              @foreach($view as $data)
              @php
                $sum += $data['budget'];
              @endphp
              <tr>
                <td align="center">{{$data['stat_year']}}</td>
                <td align="center">{{$data['center_money']}}</td>
                <td>{{$data['account']}}</td>
                <td>{{Func::get_account($data['account'])}}</td>
                <td align="right">{{number_format($data['budget'],2)}}</td>
                @if($data['status'] == 1)
                  <td align="center"><span class="badge badge-pill badge-success">อนุมัติแล้ว</span></td>
                  <?php $able = 'disabled'; ?>
                @else
                  <td align="center"><span class="badge badge-pill badge-danger">ยังไม่อนุมัติ</span></td>
                  <?php $able = ''; ?>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" align="right"><b>Sum</b></td>
                <td align="right"><b>{{ number_format($sum,2) }}</b></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          @if((Auth::user()->type == 3 || Auth::user()->type == 1) && !empty($view))
          <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal" <?php echo $able; ?>>
            <i class="nav-icon fa fa-check"></i> Approve log
          </button>
          @endif
        </div>
      </div>
    </div>
  </div>
  </main>
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <form action="{{route('post_approve')}}" method="post">
          @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Approve log</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>ต้องการอนุมัติข้อมูลงบประมาณใช่หรือไม่?</p>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="budget" value="{{$sum}}">
          <input type="hidden" name="year" value="{{$yy}}">
          <input type="hidden" name="user_approve" value="{{Auth::user()->emp_id}}">
          <button class="btn btn-primary" type="submit">Yes</button>
          {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
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
      $('.myTable').DataTable({
        select:true,
      });
  </script>

@endsection
