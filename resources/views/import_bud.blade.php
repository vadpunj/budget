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
    <li class="breadcrumb-item active">Import file budget</li>
  </ol>
   <h3 align="center">Import Excel File</h3>

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

   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
   <div class="card-body">
   <form method="post" enctype="multipart/form-data" action="{{ url('budget/import_excel/import') }}">
    {{ csrf_field() }}

    <div class="form-group row">
      <label class="col-md-2 col-form-label" for="date-input">Select File</label>
      <div class="col-md-4">
        <input id="file-input" type="file" name="select_file"><span class="text-muted">.xslx</span>
      </div>
    </div>
    <div class="col-md-4">
      <input type="submit" name="upload" class="btn btn-primary" value="Submit">
    </div><br>
   </form>
   <div class="form-group row">
     <label class="col-md-2 col-form-label" for="date-input">Year : </label>
     <div class="col-md-2">
       <select class="form-control" id="year" name="year" onchange="myFunction()">
         @for($i=2018 ;$i <= date('Y'); $i++)
         <option value="{{ $i }}" @if($i == date('Y')) selected @else '' @endif>{{ $i }}</option>
         @endfor
       </select>
     </div>
   </div>
   <div id="dvTable" style="border:0px;overflow: auto;width: 100%" class="table table-responsive-sm table-bordered"></div>

  </div>
</main>
@endsection

@section('js')
  <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js') }}"></script>
  <script>
  $(document).ready(function() {
    var x = document.getElementById("year").value;
    // console.log(x);
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
        type: 'POST',
        url: '/find/data',
        data: {year:x},
        dataType: "json",
        success: function (json) {
          console.log(json);
          if(json.success){
            var customers = new Array();
            customers.push(["list", "business", "dis_business" , "project" , "activ","respons","amount","price_per","unit","unitsap","total","explan","unit_t","year","status"]);
            json.success.forEach(myforeach);
            function myforeach(item, index) {
              customers.push([item.list,item.business,item.dis_business,item.project,item.activ,item.respons,item.amount,item.price_per,item.unit,item.unitsap,item.total,item.explan,item.unit_t,item.year,item.status]);
            }
            var table = document.createElement("TABLE");
            table.style.width = '100%';
            table.style.overflow= "auto";
            // table.border = "1";

            //Get the count of columns.
            var columnCount = customers[0].length;

            //Add the header row.
            var row = table.insertRow(-1);
            for (var i = 0; i < columnCount; i++) {
                var headerCell = document.createElement("TH");
                headerCell.innerHTML = customers[0][i];
                row.appendChild(headerCell);
            }

            //Add the data rows.
            for (var i = 1; i < customers.length; i++) {
                row = table.insertRow(-1);
                for (var j = 0; j < columnCount; j++) {
                    var cell = row.insertCell(-1);
                    cell.innerHTML = customers[i][j];
                    if(j==7 || j==10){
                      cell.innerHTML = number_format(parseFloat(customers[i][j]));
                      cell.style.textAlign = "right";
                    }else if(j==6 || j==8 || j==9 || j==12){
                      cell.innerHTML = numberCommas(customers[i][j]);
                      cell.style.textAlign = "right";
                    }else if(j==13){
                      cell.innerHTML = customers[i][j];
                      cell.style.textAlign = "center";
                    }
                }
            }
            function number_format(x) {
               return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            function numberCommas(x) {
               return x.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
            var dvTable = document.getElementById("dvTable");
            dvTable.innerHTML = "";
            // document.getElementById('dvTable').style.width= '100%';
            dvTable.appendChild(table);
          }
        },
        error: function (e) {
            console.log(e.message);
        }
    });
  });
  function myFunction() {
    var x = document.getElementById("year").value;
    // console.log(x);
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
        type: 'POST',
        url: '/find/data',
        data: {year:x},
        dataType: "json",
        success: function (json) {
          if(json.success){
            var customers = new Array();
          customers.push(["list", "business", "dis_business" , "project" , "activ","respons","amount","price_per","unit","unitsap","total","explan","unit_t","year","status"]);
            json.success.forEach(myforeach);
            function myforeach(item, index) {
              customers.push([item.list,item.business,item.dis_business,item.project,item.activ,item.respons,item.amount,item.price_per,item.unit,item.unitsap,item.total,item.explan,item.unit_t,item.year,item.status]);
            }
            var table = document.createElement("TABLE");
            table.style.width = '100%';
            table.style.overflow= "auto";
            // table.border = "1";

            //Get the count of columns.
            var columnCount = customers[0].length;

            //Add the header row.
            var row = table.insertRow(-1);
            for (var i = 0; i < columnCount; i++) {
                var headerCell = document.createElement("TH");
                headerCell.innerHTML = customers[0][i];
                row.appendChild(headerCell);
            }

            //Add the data rows.
            for (var i = 1; i < customers.length; i++) {
                row = table.insertRow(-1);
                for (var j = 0; j < columnCount; j++) {
                    var cell = row.insertCell(-1);
                    cell.innerHTML = customers[i][j];
                    if(j==7 || j==10){
                      cell.innerHTML = number_format(parseFloat(customers[i][j]));
                      cell.style.textAlign = "right";
                    }else if(j==6 || j==8 || j==9 || j==12){
                      cell.innerHTML = numberCommas(customers[i][j]);
                      cell.style.textAlign = "right";
                    }else if(j==13){
                      cell.innerHTML = customers[i][j];
                      cell.style.textAlign = "center";
                    }
                }
            }
            function number_format(x) {
               return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
           }
           function numberCommas(x) {
              return x.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
           }

            var dvTable = document.getElementById("dvTable");
            dvTable.innerHTML = "";
            dvTable.appendChild(table);
          }else{
            var dvTable = document.getElementById("dvTable");
            dvTable.innerHTML = "";
          }
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
