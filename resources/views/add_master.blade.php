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
        <a href="{{ route('dashboard') }}">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item active">ข้อมูลบัญชี</li>
    </ol>
    <!-- end breadcrumb -->
  <div class="container-fluid">
    @if(Auth::user()->type == 1 || Auth::user()->type == 5)
    <button type="button" class="btn btn-info mb-1" data-toggle="modal" data-target="{{'#myAdd'}}">
      <i class="nav-icon fa fa-plus"></i> เพิ่มข้อมูลบัญชี
    </button>
   @endif
  <div class="animated fadeIn">
    <div class="row">
       <div class="col-lg-12">
        <form action="{{ route('find_master') }}" method="post">
          @csrf
         <div class="card">
         <div class="card-header">
           <i class="fa fa-search"></i> ค้นหาข้อมูลบัญชี</div>
           <div class="card-body">
             <div class="form-group row">

             <label class="col-md-1 col-form-label">หมวดหมู่</label>
             <div class="col-md-3">
               <select class="form-control" name="id1">
                 @foreach($list as $value)
                   <option value="{{ $value->name_id }}" @if($cate == $value->name_id) selected @else '' @endif>{{ $value->name }}</option>
                 @endforeach
               </select>
             </div>
             <div class="col-md-2 form-group">
               <button class="btn btn-primary" type="submit">Submit</button>
             </div>
             </form>
            </div>
         </div>
        </div>
      </div>
    </div>
   </div>
@if(isset($data_arr))
   <table class="table table-responsive-sm table-bordered" id="myTable">
     <thead>
       <th>รายการภาระผูกพัน</th>
       <th>ชื่อ</th>
       <th>Edit</th>
       <th>Delete</th>
     </thead>
     <tbody>
    @foreach($data_arr as $arr => $id1_arr)
    @foreach($id1_arr as $arr1 => $id2_arr)
    @if($arr == 1)
    <tr>
      @if($arr1 == 1)
        <td align="center">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</td>
      @elseif($arr1 == 2)
        <td align="center">1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</td>
      @endif
    @elseif($arr == 2)
    <tr>
      @if($arr1 == 1)
        <td align="center">2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง</td>
      @elseif($arr1 == 2)
        <td align="center">2.2 ค่าสวัสดิการผู้บริหาร</td>
      @endif
    @endif
    @foreach($id2_arr as $account => $row)
      <tr>
       <td align="center">{{ $account }}</td>
       <td>{{ $row }}</td>
       <td align="center">
         {{--<a href="{{ '/estimate/edit/master/'.$row->id }}">--}}
           <button type="button" class="btn btn-warning" data-toggle="modal" data-target="{{'#myEdit'.$account}}">
             <i class="nav-icon icon-pencil"></i> Edit
           </button>
         {{--</a>--}}
       </td>
       <td align="center">
         <button type="button" class="btn btn-danger" data-toggle="modal" data-target="{{'#myDelete'.$account}}">
           <i class="nav-icon icon-trash"></i> Delete
         </button>
       </td>
      </tr>
      @endforeach
    @endforeach
    @endforeach
     </tbody>
   </table>
   @endif
  </div>
 </main>
 <div class="modal fade" id="{{'myAdd'}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
     <div class="modal-content">
       <form action="{{ route('post_add_master') }}" method="POST">
         @csrf
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูลบัญชี</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-md-2 col-form-label">รหัสรายการภาระผูกพัน <font color="red">*</font></label>
            <div class="form-group col-md-4">
              <div class="input-group">
                <input class="form-control @error('account') is-invalid @enderror" type="text" name="account" value="{{ old('account') }}">
                @error('account')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
            <label class="col-md-2 col-form-label">ชื่อ <font color="red">*</font></label>
            <div class="form-group col-md-4">
              <div class="input-group">
                <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}">
                @error('name')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
            <label class="col-md-2 col-form-label">หมวดหมู่ <font color="red">*</font></label>
            <div class="col-md-4">
              <select class="form-control" name="id1">
                @foreach($list as $value)
                <option value="{{ $value->name_id }}">{{ $value->name }}</option>
                @endforeach
              </select>
            </div>
            <label class="col-md-2 col-form-label">หมวดหมู่ย่อย <font color="red">*</font></label>
            <div class="col-md-4">
              <select class="form-control" name="id2">
                <option value="{{ '0' }}">ไม่มีหมวดหมู่ย่อย</option>
                <option value="{{ '1' }}">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</option>
                <option value="{{ '2' }}">1.2เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</option>
                <option value="{{ '1' }}">2.1ค่าสวัสดิการพนักงาน ลูกจ้าง</option>
                <option value="{{ '2' }}">2.2ค่าสวัสดิการผู้บริหาร</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Save</button>
        </div>
        </form>
      </div>

    </div>
  </div>
 @foreach($data as $row)
 <div class="modal fade" id="{{'myEdit'.$row->account}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-warning" role="document">
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
              <label for="city">รายการภาระผูกพัน <font color="red">*</font></label>
              <input class="form-control @error('account') is-invalid @enderror" type="text" name="account" value="{{$row->account}}" readonly>
              @error('account')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">ชื่อ <font color="red">*</font></label>
              <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{$row->name}}">
              <input class="form-control" type="hidden" name="account" value="{{$row->account}}">
              @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Save</button>
        </div>
        </form>
      </div>

    </div>
  </div>
  @endforeach

  @foreach($data as $row)
  <div class="modal fade" id="{{'myDelete'.$row->account}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
           <input type="hidden" name="account" value="{{ $row->account }}">
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
  <!-- <script type="text/javascript">
  $(document).ready(function() {
    $('#myTable').DataTable({
      scrollX:true
    });
  });

  </script> -->
@endsection
