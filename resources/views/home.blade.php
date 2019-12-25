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
      <li class="breadcrumb-item active">ศูนย์ต้นทุน</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
          <div class="card-header word">
            @if (session()->has('notification'))
              <div class="notification">
                {!! session('notification') !!}
              </div>
            @endif
          <i class="fa fa-align-justify"></i> กรอกศูนย์ต้นทุน</div>
            <form class="form-horizontal" action="{{ route('insert') }}" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">วันทำรายการ</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="fa fa-calendar"></i>
                        </span>
                        <input class="form-control @error('start_date') is-invalid @enderror" type="date" name="start_date">
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                      </div>
                    </div>
                    <label class="col-md-2 col-form-label">ถึง</label>
                      <div class="form-group col-sm-4">
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="fa fa-calendar"></i>
                          </span>
                          <input class="form-control @error('end_date') is-invalid @enderror" type="date" name="end_date">
                          @error('end_date')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                        </div>
                      </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">ศูนย์ต้นทุน</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('source') is-invalid @enderror" type="text" name="source">
                    @error('source')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <label class="col-md-2 col-form-label">สาขาหน่วยงาน</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('branch') is-invalid @enderror" type="text" id="branch" name="branch" onchange="myfunc()">
                    @error('branch')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <p id="demo"></p>
                  </div>
                </div>
              </div>
              <div class="col-md-2 form-group form-actions">
                <button class="btn btn-primary" type="submit">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </main>
@endsection

@section('js')
<script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script>
  function myfunc(){
    var x = document.getElementById("branch").value;
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
        type: 'POST',
        url: '/find/branch',
        data: {data:x},
        dataType: "json",
        success: function (json) {
          document.getElementById("demo").innerHTML = json.success;
        },
        error: function (e) {
            console.log(e.message);
        }
    });
  }

  </script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
