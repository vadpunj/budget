@extends('layout')

@section('title')
<title>Export to SAP</title>
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
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">Export file to SAP</li>
    </ol>
   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
   <div class="card-body">
    <div class="panel-body">
      <div class="card-body">
       <h3 align="center">Export File</h3>
        <div class="form-group">
          <button class="btn btn-primary mb-1" type="button" data-toggle="modal" data-target="#myModal">Export</button>
          {{--<a class="btn btn-primary" href="">Export</a>--}}
        </div>
      </div>
    </div>
  </div>
  </main>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title">เลือกข้อมูล Export ข้อมูล</h4>
         <button class="close" type="button" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">×</span>
         </button>
        </div>
        <form action="{{ route('export_sap') }}" method="post">
          {{ csrf_field() }}
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-md-2 col-form-label" for="email-input">ปีบัญชี (พ.ศ.)</label>
              <div class="col-md-4">
                <input class="form-control" type="number" name="stat_year" value="{{ date('Y')+543 }}">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-2 col-form-label">ศูนย์ต้นทุน</label>
              <div class="form-group col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" name="center_money1">
                </div>
              </div>
              <label class="col-md-1 col-form-label">ถึง</label>
              <div class="form-group col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" name="center_money2">
                </div>
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
  <script src="{{ asset('admin/js/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#myTable').DataTable({
      scrollX:true
    });
  });
  </script>
  @endsection
