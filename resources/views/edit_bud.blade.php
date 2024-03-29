@extends('layout')

@section('title')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Edit page</title>
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
        <a href="{{ route('dashboard') }}">หน้าแรก</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('add_bud') }}">Add Budget</a>
      </li>
      <li class="breadcrumb-item active">แก้ไขข้อมูลงบประมาณ</li>
    </ol>
    <!-- end breadcrumb -->
    <div class="container-fluid">
      @if($message = Session::get('success'))
      <div class="alert alert-success alert-block">
       <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $message }}</strong>
      </div>
      @endif
      <div class="animated fadeIn">
        <form action="{{ route('edit_bud') }}" method="POST">
          @csrf
        <div class="form-group row">
          <label class="col-md-2 col-form-label">ปีงบประมาณ</label>
          <div class="form-group col-sm-4">
            <div class="input-group">
              <input class="form-control" type="text" name="stat_year" value="{{$user_req[0]->stat_year}}">
            </div>
          </div>
          <label class="col-md-2 col-form-label">สายงาน</label>
          <div class="form-group col-sm-4">
            <div class="input-group">
              <input class="form-control" type="text" name="field" value="{{\Auth::user()->field}}" readonly="true">
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-2 col-form-label">ฝ่าย/สำนักงาน/ศูนย์</label>
          <div class="form-group col-sm-4">
            <input class="form-control" type="text" name="office" value="{{\Auth::user()->office}}" readonly="true">
          </div>
          <label class="col-md-2 col-form-label">ชื่อผู้ขอ</label>
          <div class="form-group col-sm-4">
            <input class="form-control" type="text" name="part" value="{{$user_req[0]->part}}" readonly="true">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-2 col-form-label">ชื่อผู้ขอ</label>
          <div class="form-group col-sm-4">
            <input class="form-control" type="text" name="name_reqs" value="{{$user_req[0]->name}}" >
          </div>
          <label class="col-md-2 col-form-label">เบอร์ติดต่อ</label>
          <div class="form-group col-sm-4">
            <input class="form-control" type="text" name="phone" value="{{$user_req[0]->phone}}">
            <input class="form-control" type="hidden" name="id" value="{{$user_req[0]->id}}">
          </div>
        </div>

          <div class="table-responsive">
            <table class="table table-bordered" id="dynamic_field" style="overflow-x: scroll;width:230%">
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
              @foreach($budget as $data)
                <tr>
                  <td><input type="text" name="list_old[]" class="form-control name_list" value="{{$data->list}}"/></td>
                  <td><input type="text" name="business_old[]" class="form-control name_list" value="{{$data->business}}"/></td>
                  <td><input type="text" name="dis_business_old[]" class="form-control name_list" value="{{$data->dis_business}}"/></td>
                  <td><input type="text" name="project_old[]" class="form-control name_list" value="{{$data->project}}"/></td>
                  <td><input type="text" name="activ_old[]" class="form-control name_list" value="{{$data->activ}}"/></td>
                  <td><input type="text" name="respons_old[]" class="form-control name_list" value="{{$data->respons}}"/></td>
                  <td><input type="text" name="amount_old[]" class="form-control name_list" value="{{$data->amount}}"/></td>
                  <td><input type="text" name="price_per_old[]" class="form-control name_list" value="{{$data->price_per}}"/></td>
                  <td><input type="text" name="total_old[]" class="form-control name_list" value="{{$data->total}}"/></td>
                  <td><input type="text" name="unit_old[]" class="form-control name_list" value="{{$data->unit}}"/></td>
                  <td><input type="text" name="unitsap_old[]" class="form-control name_list" value="{{$data->unitsap}}"/></td>
                  <td><input type="text" name="explan_old[]" class="form-control name_list" value="{{$data->explan}}"/></td>
                  <td><input type="text" name="unit_t_old[]" class="form-control name_list" value="{{$data->unit_t}}"/></td>
                  <td><input type="text" name="year_old[]" class="form-control name_list" value="{{$data->year}}"/></td>
                  <td><input type="text" name="status_old[]" class="form-control name_list" value="{{$data->status}}"/></td>
                  <input type="hidden" name="id_old[]" class="form-control name_list" value="{{$data->id}}"/>
                </tr>
              @endforeach
            </table>
              <div class="col-md-2 form-group form-actions">
                <button class="btn btn-success" name="add" id="edit" type="button">Add More</button>
              </div>
              <div class="col-md-2 form-group form-actions">
                <button class="btn btn-primary" id="submit" type="submit">Submit</button>
              </div>
          </div>
        </form>
      </div>
    </div>
  </main>
  @endsection



@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script>
 $(document).ready(function(){

      var i=1;
      $('#edit').click(function(){
            i++;
            $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="list[]" class="form-control name_list" /></td><td><input type="text" name="business[]" class="form-control name_list" /></td><td><input type="text" name="dis_business[]" class="form-control name_list" /></td><td><input type="text" name="project[]" class="form-control name_list" /></td><td><input type="text" name="activ[]" class="form-control name_list" /></td><td><input type="text" name="respons[]" class="form-control name_list" /></td><td><input type="text" name="amount[]" class="form-control name_list" /></td><td><input type="text" name="price_per[]" class="form-control name_list" /></td><td><input type="text" name="total[]" class="form-control name_list" /></td><td><input type="text" name="unit[]" class="form-control name_list" /></td><td><input type="text" name="unitsap[]" class="form-control name_list" /></td><td><input type="text" name="explan[]" class="form-control name_list" /></td><td><input type="text" name="unit_t[]" class="form-control name_list" /></td><td><input type="text" name="year[]" class="form-control name_list" /></td><td><input type="text" name="status[]" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove_edit">X</button></td></tr>'
            );
      });
      $(document).on('click', '.btn_remove_edit', function(){
           var button_id = $(this).attr("id");
           $('#row'+button_id+'').remove();
      });
 //      $('#submit').click(function(){
 //          $.ajaxSetup({
 //            headers: {
 //                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 //              }
 //            });
 //           $.ajax({
 //                url:"/budget/data/edit",
 //                method:"POST",
 //                data:$('#edit_name').serialize(),
 //                dataType: "json",
 //                success: function (json) {
 //                  console.log(json.success);
 //                  // alert('บันทึกข้อมูลเรียบร้อย');
 //                  $('#edit_name')[0].reset();
 //                },
 //                error: function (e) {
 //                    console.log(e.message);
 //                    alert('บันทึกข้อมูลผิดพลาด');
 //                }
 //           });
 //      });
 });
 </script>
  <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js') }}"></script>
@endsection
