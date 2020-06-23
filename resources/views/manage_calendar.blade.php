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
  <link href="{{ asset('admin/css/fullcalendar.min.css') }}" rel="stylesheet">
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
          @if($message = Session::get('success'))
          <div class="alert alert-success alert-block">
           <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $message }}</strong>
          </div>
          @endif
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
                  <div class="form-group col-md-4">
                    <div class="input-group">
                      <input class="datepicker form-control @error('start_date') is-invalid @enderror" type='text' data-provide="datepicker" name="start_date" autocomplete="off"/>
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
                    <div class="form-group col-md-4">
                      <div class="input-group">
                        <input class="datepicker form-control @error('end_date') is-invalid @enderror" name="end_date" type='text' data-provide="datepicker" autocomplete="off"/>
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
                  <div class="form-group col-md-4">
                    <input class="form-control @error('event_name') is-invalid @enderror" type="text" name="event_name" autocomplete="off">
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
           ลบ/แก้ไขกิจกรรม
          <table class="table table-responsive-sm table-bordered" >
            <thead>
              <tr>
                <th>วันที่เริ่มกิจกรรม</th>
                <th>วันที่สิ้นสุดกิจกรรม</th>
                <th>ชื่อกิจกรรม</th>
                <th>แก้ไข</th>
                <th>ลบ</th>
              </tr>
            </thead>
            @foreach($calendar as $list)
            <tbody>
              <tr>
                <td align="center">{{ $list->start_date }}</td>
                <td align="center">{{ $list->end_date }}</td>
                <td>{{ $list->event_name }}</td>
                <td align="center">
                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="{{'#myEdit'.$list->id}}">
                    <i class="nav-icon icon-pencil"></i> Edit
                  </button>
                </td>
                <td align="center">
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="{{'#myDelete'.$list->id}}">
                    <i class="nav-icon icon-trash"></i> Delete
                  </button>
                </td>
              </tr>
            </tbody>
            @endforeach
          </table>
          </div>
    </div>
    </div>
  </div>
  </main>
  @foreach($calendar as $list)
  <div class="modal fade" id="{{'myEdit'.$list->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-warning" role="document">
      <div class="modal-content">
        <form action="{{ route('editevent') }}" method="POST">
          @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">แก้ไขกิจกรรม</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">
             <div class="form-group row">
               <label class="col-md-2 col-form-label">วันที่</label>
               <div class="form-group col-md-4">
                 <div class="input-group">
                   <input type='hidden' name="id" value="{{ $list->id }}" />
                   <input class="datepicker form-control @error('start_day') is-invalid @enderror" type='text' data-provide="datepicker" name="start_day" value="{{ date("m/d/Y", strtotime($list->start_date)) }}" autocomplete="off"/>
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
                 <div class="form-group col-md-4">
                   <div class="input-group">
                     <input class="datepicker form-control @error('end_day') is-invalid @enderror" name="end_day" type='text' data-provide="datepicker" value="{{ date("m/d/Y", strtotime($list->end_date)) }}" autocomplete="off"/>
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
               <div class="form-group col-md-4">
                 <input class="form-control @error('new_event') is-invalid @enderror" type="text" name="new_event" value="{{ $list->event_name }}" autocomplete="off">
                 @error('event_name')
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
   <div class="modal fade" id="{{'myDelete'.$list->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-sm modal-danger" role="document">
       <div class="modal-content">
         <form action="{{ route('delete_event') }}" method="POST">
           @csrf
         <div class="modal-header">
           <h5 class="modal-title" id="exampleModalLabel">ลบข้อมูลกิจกรรม</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>
          <div class="modal-body">
            <p>ต้องการลบข้อมูลกิจกรรมนี้หรือไม่?</p>
            <input type="hidden" name="id" value="{{ $list->id }}">
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" type="submit">Delete</button>
          </div>
          </form>
        </div>

      </div>
    </div>
   @endforeach
@endsection

@section('js')
  <script src="{{ asset('admin/js/moment.min.js') }}"></script>
  <script src="{{ asset('admin/js/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>

@endsection
