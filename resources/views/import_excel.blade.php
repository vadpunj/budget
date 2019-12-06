@extends('layout')

@section('title')
<title>CoreUI</title>
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
  </style>
@endsection
@section('content')
<main class="main">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">หน้าแรก</a>
    </li>
    <li class="breadcrumb-item active">Import file {{ $type }}</li>
  </ol>
   <h3 align="center">Import Excel File</h3>

   @if(count($errors) > 0)
    <div class="alert alert-danger">
     Upload Validation Error<br><br>
     <ul>
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
     </ul>
    </div>
   @endif

   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
   <div class="card-body">
   <form method="post" enctype="multipart/form-data" action="{{ url($type.'/import_excel/import') }}">
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-md-2 col-form-label" for="date-input">Time Key</label>
      <div class="col-md-4">
        <input class="form-control" name="time_key" type="text" required>

      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-2 col-form-label" for="date-input">Select File</label>
      <div class="col-md-4">
        <input id="file-input" type="file" name="select_file"><span class="text-muted">.xls, .xslx</span>
      </div>
    </div>
    <div class="col-md-4">
      <input type="submit" name="upload" class="btn btn-primary" value="Submit">
    </div><br>
   </form>
   <div class="card-header word" style="width: 50%;"> ข้อมูลเดือนล่าสุดในระบบ</div>
   <table class="table table-responsive-sm table-sm" style="width: 50%;">
     <thead>
       <tr>
         <th>Time Key</th>
         <th>M Unit</th>
         <th>M Unit Price</th>
         <th>M Cost Total</th>
       </tr>
     </thead>
     @if(!empty($data))
     <tbody>
       <tr>
         <td>{{ $data->TIME_KEY}}</td>
         <td>{{ number_format($data->M_UNIT) }}</td>
         <td>{{ number_format($data->M_UNIT_PRICE,2) }}</td>
         <td>{{ number_format($data->M_Cost_TOTAL,2) }}</td>
       </tr>
     </tbody>
     @else
     <tbody>
       <tr>
         <td colspan="4" align="center">{{ 'ไม่มีข้อมูล' }}</td>
       </tr>
     </tbody>
     @endif
   </table>
  </div>
</main>
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
