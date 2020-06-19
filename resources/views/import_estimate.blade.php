@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Import page</title>
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
    <li class="breadcrumb-item active">Import file estimate</li>
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

  <div class="card-body">
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-block">
     <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
    </div>
    @endif
   <form method="post" enctype="multipart/form-data" action="{{ url('estimate/import/estimate') }}">
    {{ csrf_field() }}

    <div class="form-group row">
      <label class="col-md-2 col-form-label" for="date-input">Select File</label>
      <div class="col-md-4">
        <input id="file-input" type="file" name="select_file"><span class="text-muted">.xlsx</span>
      </div>
    </div>
    <div class="col-md-4">
      <input type="submit" name="upload" class="btn btn-primary" value="Submit">
    </div><br>
  </form><br>
  @if(!empty($data))
   <h5>รหัสรายการภาระผูกพันที่ไม่มีอยู่ในระบบ</h5>
      <table class="table table-responsive-sm table-bordered">
        <thead>
          <tr>
            <th>Year</th>
            <th>Account</th>
            <th>Budget</th>
            <th>Center Money</th>
            <th>Edit</th>
          </tr>
        </thead>
        <tbody>
        @foreach($data as $datas)
          <tr>
            <td align="center">{{ $datas['stat_year'] }}</td>
            <td>{{ $datas['account'] }}</td>
            <td align="right">{{ number_format($datas['budget'],2) }}</td>
            <td align="center">{{ $datas['center_money'] }}</td>
            <td align="center">
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="{{'#myEdit'.$datas['id']}}">
                <i class="nav-icon icon-pencil"></i> Edit
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
  @endif
  </div>

</main>
@foreach($data as $datas)
<div class="modal fade" id="{{'myEdit'.$datas['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('post_edit_account') }}" method="POST">
        @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">แก้ไขรหัสรายการภาระผูกพัน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <div class="modal-body">
         <div class="form-group row">
            <label class="col-md-4 col-form-label">รายการภาระผูกพัน</label>
            <div class="form-group col-sm-6">
             <input class="form-control @error('account') is-invalid @enderror" type="text" name="account" value="{{$datas['account']}}">
             <input type="hidden" name="id" value="{{$datas['id']}}">
             @error('account')
               <span class="invalid-feedback" role="alert">
                   <strong>{{ $message }}</strong>
               </span>
             @enderror
           </div>
         </div>
       </div>
       <div class="modal-footer">
         <button type="submit" class="btn btn-primary">Save</button>
       </div>
       </form>
     </div>

   </div>
 </div>
 @endforeach
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
