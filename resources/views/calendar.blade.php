@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Calendar page</title>
@endsection

@section('css')
  <!-- <link href="{{ asset('admin/node_modules/@coreui/icons/css/coreui-icons.min.css') }}" rel="stylesheet"> -->
  <link href="{{ asset('admin/node_modules/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/node_modules/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/datepicker.css') }}" rel="stylesheet">
  <!-- <link href="{{ asset('admin/css/main.css') }}" rel="stylesheet"> -->
  <link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css') }}" rel="stylesheet">
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
      <li class="breadcrumb-item active">ปฏิทิน</li>
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
          <i class="fa fa-align-justify"></i> เพิ่มกิจกรรม</div>
            <form class="form-horizontal" action="{{ route('addevent') }}" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">วันที่</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="datepicker form-control @error('start_date') is-invalid @enderror" type='text' data-provide="datepicker" name="start_date"/>
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
                  <label class="col-md-2 col-form-label">ถึง</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="datepicker form-control @error('end_date') is-invalid @enderror" name="end_date" type='text' data-provide="datepicker"/>
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
                  <label class="col-md-2 col-form-label">Event Name</label>
                  <div class="form-group col-sm-4">
                    <input class="form-control @error('event_name') is-invalid @enderror" type="text" name="event_name">
                    @error('event_name')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="col-md-2 form-group form-actions">
                <button class="btn btn-primary" type="submit">Submit</button>
              </div>
            </form>
          </div>
        </div>
        <div class="card-body">
          {!! $calendar_details->calendar() !!}
        </div>
      </div>
    </div>
  </div>
  </main>
@endsection

@section('js')
  <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
    {!! $calendar_details->script() !!}
@endsection
