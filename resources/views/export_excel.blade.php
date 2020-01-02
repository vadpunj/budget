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
      <li class="breadcrumb-item active">Export file budget</li>
    </ol>
   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
    <div class="panel-body">
      <div class="card-body">
       <h3 align="center">Export File</h3>
        <div class="form-group">
          <button class="btn btn-primary mb-1" type="button" data-toggle="modal" data-target="#myModal">Export</button>
          {{--<a class="btn btn-primary" href="{{url('budget/export_excel/export')}}">Export</a>--}}
        </div>

      <div class="panel panel-default">
        <div class="panel-heading">
         <h3 class="panel-title">{{ 'Budget Data'}}</h3>
        </div>
        <table class="table table-responsive-sm table-bordered" id="myTable">
          <thead>
            <th>Year</th>
            <th>Branch</th>
            <th>List</th>
            <th>Detail</th>
            <th>Money</th>
            <th>Remark</th>
          </thead>
          <tbody>
         @foreach($data as $row)
           <tr>
            <td align="center">{{ $row->year }}</td>
            <td>{{ $row->branch }}</td>
            <td>{{ $row->list }}</td>
            <td>{{ $row->detail }}</td>
            <td align="right">{{ number_format($row->money,2) }}</td>
            <td>{{ $row->remark }}</td>
           </tr>
         @endforeach
          </tbody>
        </table>
      </div>
      </div>
    </div>
  </main>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title">เลือกวันที่ Export ข้อมูล</h4>
         <button class="close" type="button" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">×</span>
         </button>
        </div>
        <form action="{{ url('budget/export_excel/export') }}" method="post">
          {{ csrf_field() }}
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-md-3 col-form-label" for="email-input">Time Key</label>
              <div class="col-md-9">
                <input class="form-control" type="number" name="date" autocomplete="email">
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
