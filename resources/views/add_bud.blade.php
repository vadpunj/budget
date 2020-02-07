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
      <li class="breadcrumb-item active">เพิ่มข้อมูลงบประมาณลงทุน</li>
    </ol>
    <!-- end breadcrumb -->
    <div class="container-fluid">
      <div class="animated fadeIn">
        <div align="right">
          <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
            <i class="nav-icon fa fa-plus"></i> Add
          </button>
        </div><br>
            @csrf
            <table class="table table-responsive-sm table-bordered" style="overflow-x: scroll;width:100%">
              <thead>
                <tr>
                  <th>ปีงบประมาณ</th>
                  <th>สายงาน</th>
                  <th>ฝ่าย/สำนักงาน/ศูนย์</th>
                  <th>ส่วน/สค.</th>
                  <th>ชื่อผู้ขอ</th>
                  <th>เบอร์ติดต่อ</th>
                  <th>แก้ไข</th>
                </tr>
              </thead>
              <tbody>
                @foreach($user_req as $value)
                <tr>
                  <td>{{$value->stat_year}}</td>
                  <td>{{$value->field}}</td>
                  <td>{{$value->office}}</td>
                  <td>{{$value->part}}</td>
                  <td>{{$value->name}}</td>
                  <td>{{$value->phone}}</td>
                  <td>
                    <a href="{{'/budget/post/edit/'.$value->id}}">
                      <button class="btn btn-warning" name="edit" type="submit">
                        <i class="nav-icon icon-pencil"></i> Edit
                      </button>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        </div>
    </div>
  </main>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form name="add_name" id="add_name">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ข้อมูลผู้ขอ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group row">
            <label class="col-md-2 col-form-label">ปีงบประมาณ</label>
            <div class="form-group col-sm-4">
              <div class="input-group">
                <input class="form-control" type="text" name="stat_year">
              </div>
            </div>
            <label class="col-md-2 col-form-label">สายงาน</label>
            <div class="form-group col-sm-4">
              <div class="input-group">
                <input class="form-control" type="text" name="field" value="{{\Auth::user()->field}}" disabled>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-form-label">ฝ่าย/สำนักงาน/ศูนย์</label>
            <div class="form-group col-sm-4">
              <input class="form-control" type="text" name="office" value="{{\Auth::user()->office}}" disabled>
            </div>
            <label class="col-md-2 col-form-label">ส่วน/สค.</label>
            <div class="form-group col-sm-4">
              <input class="form-control @error('part') is-invalid @enderror" type="text" name="part">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-form-label">ชื่อผู้ขอ</label>
            <div class="form-group col-sm-4">
              <input class="form-control" type="text" name="name_reqs">
            </div>
            <label class="col-md-2 col-form-label">เบอร์ติดต่อ</label>
            <div class="form-group col-sm-4">
              <input class="form-control" type="text" name="phone">
            </div>
          </div>
            <div class="table-responsive">
              <table class="table table-bordered" style="overflow-x: scroll;width:230%" id="dynamic_field">
                <tr>
                  <th rowspan="2">รายการ</th>
                  <th rowspan="2">สายงานธุรกิจ</th>
                  <th rowspan="2">เขตธุรกิจ</th>
                  <th rowspan="2">ประเภทโครงการ</th>
                  <th rowspan="2">กลุ่มบริการ/กลุ่มกิจกรรม</th>
                  <th rowspan="2">ผู้รับผิดชอบ</th>
                  <th colspan="3">งบประมาณขอตั้ง</th>
                  <th rowspan="2">หน่วยนับ</th>
                  <th rowspan="2">หน่วยนับ SAP</th>
                  <th rowspan="2">คำชี้แจง</th>
                  <th colspan="3">ทรัพย์สินเดิม</th>
                </tr>
                <tr>
                  <th>จำนวนหน่วย</th>
                  <th>ราคาต่อหน่วย</th>
                  <th>รวมเงิน</th>
                  <th>จำนวนหน่วย</th>
                  <th>ปีที่จัดซื้อ</th>
                  <th>สภาพ</th>
                </tr>
                <tr>
                  <td><input type="text" name="list[]" class="form-control name_list" /></td>
                  <td><input type="text" name="business[]" class="form-control name_list" /></td>
                  <td><input type="text" name="dis_business[]" class="form-control name_list" /></td>
                  <td><input type="text" name="project[]" class="form-control name_list" /></td>
                  <td><input type="text" name="activ[]" class="form-control name_list" /></td>
                  <td><input type="text" name="respons[]" class="form-control name_list" /></td>
                  <td><input type="text" name="amount[]" class="form-control name_list" /></td>
                  <td><input type="text" name="price_per[]" class="form-control name_list" /></td>
                  <td><input type="text" name="unit[]" class="form-control name_list" /></td>
                  <td><input type="text" name="unitsap[]" class="form-control name_list" /></td>
                  <td><input type="text" name="total[]" class="form-control name_list" /></td>
                  <td><input type="text" name="explan[]" class="form-control name_list" /></td>
                  <td><input type="text" name="unit_t[]" class="form-control name_list" /></td>
                  <td><input type="text" name="year[]" class="form-control name_list" /></td>
                  <td><input type="text" name="status[]" class="form-control name_list" /></td>
                </tr>
              </table>
                <div class="col-md-2 form-group form-actions">
                  <button class="btn btn-success" name="add" id="add" type="button">Add More</button>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="submit" type="button">Submit</button>
        {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
      </div>
    </form>
    </div>
  </div>
</div>
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script>
 $(document).ready(function(){
   //---------------- add data ----------------------
   var i=1;
   $('#add').click(function(){
        i++;
        $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="list[]" class="form-control name_list" /></td><td><input type="text" name="business[]" class="form-control name_list" /></td><td><input type="text" name="dis_business[]" class="form-control name_list" /></td><td><input type="text" name="project[]" class="form-control name_list" /></td><td><input type="text" name="activ[]" class="form-control name_list" /></td><td><input type="text" name="respons[]" class="form-control name_list" /></td><td><input type="text" name="amount[]" class="form-control name_list" /></td><td><input type="text" name="price_per[]" class="form-control name_list" /></td><td><input type="text" name="unit[]" class="form-control name_list" /></td><td><input type="text" name="unitsap[]" class="form-control name_list" /></td><td><input type="text" name="total[]" class="form-control name_list" /></td><td><input type="text" name="explan[]" class="form-control name_list" /></td><td><input type="text" name="unit_t[]" class="form-control name_list" /></td><td><input type="text" name="year[]" class="form-control name_list" /></td><td><input type="text" name="status[]" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>'
      );
   });
   $(document).on('click', '.btn_remove', function(){
        var button_id = $(this).attr("id");
        $('#row'+button_id+'').remove();
   });
   $('#submit').click(function(){

   // console.log($('#add_name').serialize());
       $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });
        $.ajax({
             url:"/budget/data",
             method:"POST",
             data:$('#add_name').serialize(),
             dataType: "json",
             success: function (json) {
               console.log(json.success);
               alert('บันทึกข้อมูลเรียบร้อย');
               window.location.reload(true);
               $('#add_name')[0].reset();
             },
             error: function (e) {
                 console.log(e.message);
                 alert('บันทึกข้อมูลผิดพลาด');
             }
        });
   });

 });
 </script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
