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
  <link href="{{ asset('admin/css/datepicker.css') }}" rel="stylesheet">

  <!-- Global site tag (gtag.js) - Google Analytics-->
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker-thai.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.th.js') }}"></script>
  <script>
    $(document).ready(function(){
      $(".datepicker").datepicker();
    });
  </script>
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
      <li class="breadcrumb-item active">ปิดระบบ</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    @if(Auth::user()->type == 1 || Auth::user()->type == 5)
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
            <i class="fa fa-align-justify"></i> วันเวลาที่ปิดระบบ</div>
            <div class="card-body">
              <form action="{{ route('post_shutdown') }}" method="post">
                @csrf
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">วันเริ่มต้น <font color="red">*</font></label>
                  <div class="form-group col-md-4">
                    <div class="input-group">
                      <input class="datepicker form-control @error('start_date') is-invalid @enderror" type='text' data-provide="datepicker" name="start_date" autocomplete="off" value="{{ old('start_date') }}"/>
                      <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger " disabled="">
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                        </button>
                      </div>
                      @error('start_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <label class="col-md-2 col-form-label">วันสิ้นสุด <font color="red">*</font></label>
                  <div class="form-group col-md-4">
                    <div class="input-group">
                      <input class="datepicker form-control @error('end_date') is-invalid @enderror" type='text' data-provide="datepicker" name="end_date" autocomplete="off" value="{{ old('end_date') }}"/>
                      <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger " disabled="">
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                        </button>
                      </div>
                      @error('end_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-2 form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                  </div>
                </div>
              </form>
              @if(isset($status))
              <font color="red">{{ $status }}</font>
              @endif
          </div>
         </div>
       </div>
     </div>
   </div>
   @endif
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
