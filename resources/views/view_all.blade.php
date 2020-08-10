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
          <form action="{{ route('post_view') }}" method="post">
            @csrf
          <div class="form-group row">
            <label class="col-md-1 col-form-label" for="date-input">ปี : </label>
            <div class="col-md-2">
                <select class="form-control" name="year">
                  @for($i = (date('Y')+543) ;$i >= (date('Y',strtotime("-3 year"))+543) ; $i--)
                  <option value="{{ $i }}" @if($i == $yy) selected @else '' @endif>{{ $i }}</option>
                  @endfor
                </select>
            </div>
            <label class="col-md-2 col-form-label" for="date-input">ศูนย์ต้นทุน : </label>
            <div class="col-md-2">
                <select class="form-control selectpicker" data-live-search="true" name="center_money">
                  @if(isset($views))
                    @foreach($center_money as $data_center)
                    <option value="{{ $data_center->center_money }}" @if($center == $data_center->center_money) selected @else '' @endif>{{ $data_center->center_money }}</option>
                    @endforeach
                  @else
                    <option value="{{ 0 }}" selected >{{ 'ไม่มีข้อมูล' }}</option>
                  @endif
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
        @if(isset($views))
        <form action="{{route('post_approve')}}" method="post">
          @csrf
          <table class="table table-responsive-sm table-bordered myTable">
            <thead>
              <tr>
                <th>ปีงบประมาณ</th>
                <th>ศูนย์เงินทุน</th>
                <th>หมวดค่าใช้จ่าย</th>
                <th>รายการภาระผูกพัน</th>
                <th>งบประมาณ</th>
                @if(Auth::user()->type == 4 || Auth::user()->type == 1)
                <th>ฝ่าย/เขต</th>
                @endif
                @if(Auth::user()->type == 5 || Auth::user()->type == 1)
                <th>วง.</th>
                @endif
                <th>สถานะ</th>
              </tr>
            </thead>
            <tbody>
              @php
                $sum =0;
              @endphp

              @foreach($views as $data)
              @php
                $sum += $data['budget'];
              @endphp
              <tr>
                <td align="center">{{$data['stat_year']}}</td>
                <td align="center">{{$data['center_money']}}</td>
                <td>{{$data['account']}}</td>
                <td>{{Func::get_account($data['account'])}}</td>
                <td align="right">{{number_format($data['budget'],2)}}</td>
                @if(Auth::user()->type == 4 || Auth::user()->type == 1)
                <?php
                  $able = '';
                  if($data['status'] == "1"){
                    $able = 'disabled';
                  }
                 ?>
                  <td align="center"><input type="checkbox" name="approve1[]" value="{{$data['account']}}" <?php echo $able; ?>></td>
                @endif

                @if(Auth::user()->type == 5 || Auth::user()->type == 1)
                <?php
                  $able = 'disabled';
                  if($data['status'] == "0" || $data['status'] == "1"){
                    $able = '';
                  }
                 ?>
                  <td align="center"><input type="checkbox" name="approve2[]" value="{{$data['account']}}" <?php echo $able; ?>></td>
                  <input type="hidden" name="budget[]" value="{{$data['budget']}}" <?php echo $able; ?>>
                @endif
                <input type="hidden" name="year" value="{{$yy}}">
                <input type="hidden" name="center_money" value="{{$center}}">
                @if($data['status'] == "0")
                  <td align="center"><span class="badge badge-pill badge-warning">ฝ่าย/เขต อนุมัติแล้ว</span></td>
                @elseif($data['status'] == "1")
                  <td align="center"><span class="badge badge-pill badge-success">งบประมาณอนุมัติแล้ว</span></td>
                @elseif($data['status'] == NULL)
                  <td align="center"><span class="badge badge-pill badge-danger">งบประมาณรอพิจารณา</span></td>
                @elseif($data['status'] == "4")
                  <td align="center"><span class="badge badge-pill badge-danger">แก้ไขงบประมาณ</span></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" align="right"><b>Sum</b></td>
                <td align="right"><b>{{ number_format($sum,2) }}</b></td>
                @if(Auth::user()->type == 4 || Auth::user()->type == 1)
                  <td>Select All <input type="checkbox" id="all1" onclick='selectAll1()'></td>
                @endif
                @if(Auth::user()->type == 5 || Auth::user()->type == 1)
                  <td>Select All <input type="checkbox" id="all2" onclick='selectAll2()'></td>
                @endif
                <td></td>
              </tr>
            </tfoot>
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
      });
      function selectAll1() {
        // console.log($('#all1').is(':checked'));
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
