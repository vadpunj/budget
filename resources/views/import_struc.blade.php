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
  <link href="{{ asset('admin/css/jquery.dataTables.css') }}" rel="stylesheet">
  <script src="{{ asset('admin/js/jquery-1.12.0.js') }}"></script>
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
    <li class="breadcrumb-item active">Add/Import file structure</li>
  </ol>
   <h3 align="center">Add/Import Excel File</h3>

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
      <div class="animated fadeIn">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
            <div class="card-header word">
              <i class="fa fa-align-justify"></i> เพิ่มข้อมูลโครงสร้าง</div>
              <div class="card-body">
                <form action="{{ route('post_add_struc') }}" method="post">
                  @csrf
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">บริษัท</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('Company') is-invalid @enderror" type="text" name="Company">
                      </div>
                    </div>
                    <label class="col-md-2 col-form-label">สายงาน</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('Division') is-invalid @enderror" type="text" name="Division">
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ศูนย์เงินทุน</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('FundsCenterID') is-invalid @enderror" type="text" name="FundsCenterID">
                      </div>
                    </div>
                    <label class="col-md-2 col-form-label">ศูนย์ต้นทุน</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('CostCenterID') is-invalid @enderror" type="text" name="CostCenterID">
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-2 col-form-label">ฝ่าย</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('CostCenterTitle') is-invalid @enderror" type="text" name="CostCenterTitle">
                      </div>
                    </div>
                    <label class="col-md-2 col-form-label">ส่วน</label>
                    <div class="form-group col-sm-4">
                      <div class="input-group">
                        <input class="form-control @error('CostCenterName') is-invalid @enderror" type="text" name="CostCenterName">
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
   <form method="post" enctype="multipart/form-data" action="{{ route('import_struc') }}">
    @csrf
    <div class="form-group row">
      <label class="col-md-2 col-form-label" for="date-input">Select File</label>
      <div class="col-md-6">
        <input id="file-input" type="file" name="select_file"><span class="text-muted">.xlsx<a href="{{ url('/download/struct.xlsx') }}" target="_blank">
    ตัวอย่างไฟล์ที่อัพโหลด
</a></span>
      </div>
        <input type="submit" name="upload" class="btn btn-primary" value="Submit">
    </div>
    <br>
  </form><br>
   <table class="table table-responsive-sm table-bordered myTable">
     <thead>
       <tr>
         <th>No.</th>
         <th>Company</th>
         <th>Division</th>
         <th>FundsCenterID</th>
         <th>CostCenterID</th>
         <th>CostCenterTitle</th>
         <th>CostCenterName</th>
         <th>Edit</th>
         <th>Delete</th>
       </tr>
     </thead>
     <tbody>
       @foreach($data as $row)
       <tr>
         <td align="center">{{$loop->iteration}}</td>
         <td align="center">{{$row->Company}}</td>
         <td>{{$row->Division}}</td>
         <td align="center">{{$row->FundsCenterID}}</td>
         <td align="center">{{$row->CostCenterID}}</td>
         <td>{{$row->CostCenterTitle}}</td>
         <td>{{$row->CostCenterName}}</td>
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
   @foreach($data as $row)
   <div class="modal fade" id="{{'myEdit'.$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-warning" role="document">
       <div class="modal-content">
         <form action="{{ route('edit_struc') }}" method="POST">
           @csrf
         <div class="modal-header">
           <h5 class="modal-title" id="exampleModalLabel">แก้ไขข้อมูลโครงสร้าง</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>
          <div class="modal-body">
            <div class="row">
              <div class="form-group col-sm-4">
                <label for="city">Company</label>
                <input class="form-control @error('Company') is-invalid @enderror" type="text" name="Company" value="{{$row->Company}}">
              </div>
              <div class="form-group col-sm-8">
                <label for="postal-code">Division</label>
                <input class="form-control @error('Division') is-invalid @enderror" type="text" name="Division" value="{{$row->Division}}">
                <input class="form-control" type="hidden" name="id" value="{{$row->id}}">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-4">
                <label for="city">FundsCenter ID</label>
                <input class="form-control @error('FundsCenterID') is-invalid @enderror" type="text" name="FundsCenterID" value="{{$row->FundsCenterID}}">
              </div>
              <div class="form-group col-sm-8">
                <label for="postal-code">CostCenter ID</label>
                <input class="form-control @error('CostCenterID') is-invalid @enderror" type="text" name="CostCenterID" value="{{$row->CostCenterID}}">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-4">
                <label for="city">CostCenter Title</label>
                <input class="form-control @error('CostCenterTitle') is-invalid @enderror" type="text" name="CostCenterTitle" value="{{$row->CostCenterTitle}}">
              </div>
              <div class="form-group col-sm-8">
                <label for="postal-code">CostCenter Name</label>
                <input class="form-control @error('CostCenterName') is-invalid @enderror" type="text" name="CostCenterName" value="{{$row->CostCenterName}}">
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
          <form action="{{ route('delete_struc') }}" method="POST">
            @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ลบข้อมูลโครงสร้าง</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           <div class="modal-body">
             <p>ต้องการลบข้อมูลโครงสร้างนี้หรือไม่?</p>
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
     </div>
</main>
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
      $('.myTable').DataTable({
        select:true,
      });
  </script>
@endsection
