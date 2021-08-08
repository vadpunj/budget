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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


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
      <li class="breadcrumb-item active">ข้อมูลเวอร์ชันงบประมาณที่บันทึกเข้ามา</li>
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
          <form action="{{ route('post_version') }}" method="post">
            @csrf
          <div class="form-group row">
            <label class="col-md-1 col-form-label" for="date-input">Version : </label>
            <div class="col-md-2">
              <select class="form-control" name="version">
                @for($i = $versions ;$i >= 1 ; $i--)
                <option value="{{ $i }}" @if($i == $version) selected @else '' @endif>{{ $i }}</option>
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
                <th>หมวดค่าใช้จ่าย</th>
                <th>รายการภาระผูกพัน</th>
                <th>งบประมาณ</th>
              </tr>
            </thead>
            <tbody>
              @php
                $sum = 0;
              @endphp
              @foreach($data as $key)
              @php
                $sum += round($key->budget,2);
              @endphp
              <tr>
                <td align="center">{{ $key->stat_year }}</td>
                <td align="center">{{ $key->center_money }}</td>
                <td>{{ $key->account }}</td>
                <td>{{ Func::get_account($key->account) }}</td>
                <td align="right">{{ number_format($key->budget,2) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <td align="right" colspan="4"><b>Sum</b></td>
              <td align="right"><b>{{ number_format($sum,2) }}</b></td>
            </tfoot>
          </table>
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
