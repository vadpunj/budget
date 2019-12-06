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
    <title>Register page</title>
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
  </head>
  <body class="app flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card mx-4">
            <form method="POST" action="{{ route('register') }}">
              @csrf
              <div class="card-body p-4">
                <h1>Register</h1>
                <p class="text-muted">Create your account</p>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-user"></i>
                    </span>
                  </div>
                  <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Name" name="name" value="{{ old('name') }}" required autocomplete="name">
                  @if (session()->has('notification'))
                    <div class="notification">
                        {!! session('notification') !!}
                    </div>
                  @endif
                  @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="icon-people"></i>
                    </span>
                  </div>
                  <input class="form-control @error('emp_id') is-invalid @enderror" type="text" placeholder="Employee ID" name="emp_id" value="{{ old('emp_id') }}" required autocomplete="emp_id">
                  @error('emp_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>

                <button class="btn btn-block btn-success" type="submit">Create Account</button>
              </div>
            </form>
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
