@extends('layout')

@section('title')
<title>View user page</title>
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
      <li class="breadcrumb-item active">ข้อมูลผู้ใช้</li>
    </ol>
    <!-- end breadcrumb -->
    <div class="container-fluid">
      @if($message = Session::get('success'))
      <div class="alert alert-success alert-block">
       <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
      </div>
      @endif
    <table class="table table-responsive-sm table-bordered" id="myTable">
      <thead>
        <th>ลำดับที่</th>
        <th>ชื่อ</th>
        <th>รหัสพนักงาน</th>
        <th>สิทธิ์</th>
        <th>เบอร์</th>
        <th>View</th>
        <th>Edit</th>
        <th>Delete</th>
      </thead>
      <tbody>
      @foreach($list as $row)
       <tr>
        <td align="center">{{ $loop->iteration }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->emp_id }}</td>
        <td align="center">{{ Func::get_role($row->type) }}</td>
        <td align="center">{{ ($row->tel) }}</td>
        <td align="center">
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="{{'#myView'.$row->id}}">
            <i class="fa fa-eye"></i> View
          </button>
        </td>
        <td align="center">
          <button type="button" class="btn btn-warning" data-toggle="modal" data-target="{{'#myEdit'.$row->id}}">
            <i class="nav-icon icon-pencil"></i> Edit
          </button>
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

  </div>
 </main>

 @foreach($list as $row)
 <div class="modal fade" id="{{'myEdit'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-warning" role="document">
     <div class="modal-content">
       <form action="{{ route('list_edit_user') }}" method="POST">
         @csrf
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">แก้ไขข้อมูลผู้ใช้</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="city">ชื่อ</label>
              <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{$row->name}}">
              @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">รหัสพนักงาน</label>
              <input class="form-control" type="text" name="emp_id" value="{{$row->emp_id}}" readonly>
              <input class="form-control" type="hidden" name="id" value="{{$row->id}}">
              @error('emp_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="city">สายงาน</label>
              <input class="form-control @error('field') is-invalid @enderror" type="text" name="field" value="{{$row->field}}">
              @error('field')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">ฝ่าย/สำนักงาน</label>
              <input class="form-control @error('office') is-invalid @enderror" type="text" name="office" value="{{$row->office}}">
              @error('office')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="city">ส่วน/สค.</label>
              <input class="form-control @error('part') is-invalid @enderror" type="text" name="part" value="{{$row->part}}">
              @error('part')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">ศูนย์เงินทุน</label>
              <input class="form-control @error('center_money') is-invalid @enderror" type="text" name="center_money" value="{{$row->center_money}}">
              @error('center_money')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="city">เบอร์</label>
              <input class="form-control @error('tel') is-invalid @enderror" type="text" name="tel" value="{{$row->tel}}">
              @error('tel')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="form-group col-sm-6">
              <label for="postal-code">สิทธิ์</label>

              <select class="form-control" name="type">
                @foreach($roles as $data)
                {{ 'edeee'.$data->id }}
                  <option value="{{$data->id}}" @if($data->id == $row->type) selected @else '' @endif>{{ ucfirst($data->role_name) }}</option>
                @endforeach
              </select>
              @error('type')
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
  <div class="modal fade" id="{{'myView'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-info" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ข้อมูลผู้ใช้</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">
           <div class="row">
             <div class="form-group col-sm-6">
               <label for="city">ชื่อ</label>
               <input class="form-control" type="text" name="name" value="{{$row->name}}" readonly>

             </div>
             <div class="form-group col-sm-6">
               <label for="postal-code">รหัสพนักงาน</label>
               <input class="form-control" type="text" name="emp_id" value="{{$row->emp_id}}" readonly>

             </div>
           </div>
           <div class="row">
             <div class="form-group col-sm-6">
               <label for="city">สายงาน</label>
               <input class="form-control" type="text" name="field" value="{{$row->field}}"readonly>

             </div>
             <div class="form-group col-sm-6">
               <label for="postal-code">ฝ่าย/สำนักงาน</label>
               <input class="form-control" type="text" name="office" value="{{$row->office}}"readonly>

             </div>
           </div>
           <div class="row">
             <div class="form-group col-sm-6">
               <label for="city">ส่วน/สค.</label>
               <input class="form-control" type="text" name="part" value="{{$row->part}}"readonly>

             </div>
             <div class="form-group col-sm-6">
               <label for="postal-code">ศูนย์เงินทุน</label>
               <input class="form-control" type="text" name="center_money" value="{{$row->center_money}}"readonly>

             </div>
           </div>
           <div class="row">
             <div class="form-group col-sm-6">
               <label for="city">เบอร์</label>
               <input class="form-control" type="text" name="tel" value="{{$row->tel}}"readonly>

             </div>
             <div class="form-group col-sm-6">
               <label for="postal-code">สิทธิ์</label>
               <input class="form-control" type="text" name="type" value="{{ ucwords(Func::get_role($row->type)) }}"readonly>

             </div>
           </div>
         </div>
       </div>

     </div>
   </div>
  <div class="modal fade" id="{{'myDelete'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-danger" role="document">
      <div class="modal-content">
        <form action="{{ route('list_delete_user') }}" method="POST">
          @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ลบข้อมูลผู้ใช้งาน</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">
           <p>ต้องการลบข้อมูลผู้ใช้หรือไม่?</p>
           <input type="hidden" name="id" value="{{ $row->id }}">
         </div>
         <div class="modal-footer">
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
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
