<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="text/html">
    <link rel="shortcut icon" type="image/png" href="{{ asset('admin/img/Logononame.png') }}" sizes="16x16">
    @section('title')
    @show
    <title>Budget Project</title>
    @yield('css')
  </head>
  <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    @include('navbar')
      <div class="app-body">
        @include('sidebar')
        @yield('content')
      </div>
    @yield('js')
  </body>
</html>
