<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @section('title')
    @show
    <title>CoreUI Free Bootstrap Admin Template</title>
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
