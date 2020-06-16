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
  <link href="{{ asset('admin/css/jquery.dataTables.css') }}" rel="stylesheet">

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
      <li class="breadcrumb-item active">เพิ่มข้อมูลบัญชี</li>
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
            <i class="fa fa-align-justify"></i> เพิ่มข้อมูลบัญชี</div>
            <div class="card-body">
              <form action="{{ route('post_add_master') }}" method="post">
                @csrf
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">รายการภาระผูกพัน</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control @error('account') is-invalid @enderror" type="text" name="account">
                      @error('account')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <label class="col-md-2 col-form-label">ชื่อ</label>
                  <div class="form-group col-sm-4">
                    <div class="input-group">
                      <input class="form-control @error('name') is-invalid @enderror" type="text" name="name">
                      @error('name')
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
          </div>
         </div>
       </div>
     </div>
   </div>
   <table class="table table-responsive-sm table-bordered" id="myTable">
     <thead>
       <th>รายการภาระผูกพัน</th>
       <th>ชื่อ</th>
       <th>Edit</th>
       <th>Delete</th>
     </thead>
     <tbody>
    @foreach($data as $row)
      <tr>
       <td align="center">{{ $row->account }}</td>
       <td>{{ $row->name }}</td>
       <td align="center">
         {{--<a href="{{ '/estimate/edit/master/'.$row->id }}">--}}
           <button type="button" class="btn btn-warning" data-toggle="modal" data-target="{{'#myEdit'.$row->id}}">
             <i class="nav-icon icon-pencil"></i> Edit
           </button>
         {{--</a>--}}
       </td>
       <td align="center">
         <button type="button" class="btn btn-danger" data-toggle="modal" data-target="{{'#myDelete'.$row->id}}">
           <i class="nav-icon icon-trash"></i> Delete
         </button>
       </td>
      </tr>
    @endforeach
     </tbody>
   </table>
 </main>
 @foreach($data as $row)
 <div class="modal fade" id="{{'myEdit'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-warning" role="document">
     <div class="modal-content">
       <form action="{{ route('post_edit_master') }}" method="POST">
         @csrf
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">แก้ไขรายการภาระผูกพัน</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="city">รายการภาระผูกพัน</label>
              <input class="form-control @error('account') is-invalid @enderror" type="text" name="account" value="{{$row->account}}">
              @error('account')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">ชื่อ</label>
              <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{$row->name}}">
              <input class="form-control" type="hidden" name="id" value="{{$row->id}}">
              @error('name')
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

  @foreach($data as $row)
  <div class="modal fade" id="{{'myDelete'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-danger" role="document">
      <div class="modal-content">
        <form action="{{ route('post_delete_master') }}" method="POST">
          @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ลบข้อมูลค่าใช้จ่าย</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">
           <p>ต้องการลบข้อมูลค่าใช้จ่ายนี้หรือไม่?</p>
           <input type="hidden" name="id" value="{{ $row->id }}">
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
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/jquery.dataTables.js') }}"></script>
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
