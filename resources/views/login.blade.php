<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v2.1.15
* @link https://coreui.io
* Copyright (c) 2018 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->

<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>Login page</title>
    <!-- Icons-->
    <link href="{{ asset('admin/node_modules/@coreui/icons/css/coreui-icons.min.css')}}" rel="stylesheet">
    <link href="{{ asset('admin/node_modules/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet">
    <link href="{{ asset('admin/node_modules/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('admin/node_modules/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{ asset('admin/css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css')}}" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics-->
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      // Shared ID
      gtag('config', 'UA-118965717-3');
      // Bootstrap ID
      gtag('config', 'UA-118965717-5');
    </script>
    <style>

      .shadow {
        padding: 10px !important;
        box-shadow: 5px 10px 18px #333333 !important;
      }
    </style>

  </head>
  <body class="app flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card-group">
            <div class="card p-4 shadow">
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="card-body">
                  @if(session()->has('message'))
                      <div class="alert alert-danger">
                          {{ session()->get('message') }}
                      </div>
                  @endif
                  <h1>Login</h1>
                  <p class="text-muted">**ใช้รหัสผ่านเดียวกับ Email**</p>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="icon-user"></i>
                      </span>
                    </div>
                    <input class="form-control @error('emp_id') is-invalid @enderror" name="emp_id" value="{{ old('emp_id') }}" type="text" required placeholder="รหัสพนักงาน 8 หลัก">
                    @error('emp_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="input-group mb-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="icon-lock"></i>
                      </span>
                    </div>
                    <input class="form-control @error('password') is-invalid @enderror" type="password" placeholder="รหัสผ่าน" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <button class="btn btn-primary px-4" type="submit">Login</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('admin/node_modules/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{ asset('admin/node_modules/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{ asset('admin/node_modules/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('admin/node_modules/pace-progress/pace.min.js')}}"></script>
    <script src="{{ asset('admin/node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ asset('admin/node_modules/@coreui/coreui/dist/js/coreui.min.js')}}"></script>
  </body>
</html>
