
<div class="sidebar">
  <nav class="sidebar-nav">
    <ul class="nav">
      <!-- <li class="nav-title">จัดการระบบ</li> -->
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/dashboard')) ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="nav-icon icon-home"></i> หน้าแรก</a>
      </li>
      {{--<li class="nav-item">
        <a class="nav-link {{ (request()->is('/home')) ? 'active' : '' }}" href="{{ route('home') }}">
          <i class="nav-icon icon-pencil"></i> ศูนย์ต้นทุน</a>
      </li>--}}
      {{--<li class="nav-item">
        <a class="nav-link {{ (request()->is('/event')) ? 'active' : '' }}" href="{{ route('event') }}">
          <i class="nav-icon icon-calendar"></i> ปฏิทิน</a>
      </li>--}}
      <li class="nav-item nav-dropdown {{ (request()->is('event/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon icon-calendar"></i> ปฏิทิน</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('event')) ? 'active' : '' }}" href="{{ route('event') }}">
              <i class="nav-icon icon-calendar"></i> ปฏิทินกิจกรรม</a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ (request()->is('event/manage')) ? 'active' : '' }}" href="{{ route('manage') }}">
              <i class="nav-icon icon-doc"></i> การจัดการปฏิทิน</a>
          </li>
        </ul>
      </li>
      @if(Auth::user()->type ==1)
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('import/struc')) ? 'active' : '' }}" href="{{ route('import_struc') }}">
          <i class="nav-icon icon-doc"></i> อัพโหลดข้อมูลโครงสร้าง</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('master')) ? 'active' : '' }}" href="{{ route('import_master') }}">
          <i class="nav-icon icon-doc"></i> อัพโหลดข้อมูลค่าใช้จ่าย</a>
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('add/*')) ? 'active' : '' }}" href="{{ route('add_master') }}">
          <i class="nav-icon fa fa-plus"></i> ข้อมูลค่าใช้จ่าย</a>
      </li>
      <li class="nav-item nav-dropdown {{ (request()->is('estimate/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           งบประมาณทำการประจำปี</a>
        <ul class="nav-dropdown-items">
        @if(Auth::user()->type == 2 || Auth::user()->type == 3 || Auth::user()->type == 1)
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/add/*')) ? 'active' : '' }}" href="{{ route('add_est') }}">
              <i class="nav-icon fa fa-plus"></i> ตั้งงบประมาณทำการ</a>
          </li>
        @endif
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/import/estimate')) ? 'active' : '' }}" href="{{ route('import_estimate') }}">
              <i class="nav-icon icon-doc"></i> อัพโหลดงบประมาณทำการ</a>
          </li>
        </ul>
      </li>
      @if(Auth::user()->type == 1 || Auth::user()->type == 4 || Auth::user()->type == 5)
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/view/estimate')) ? 'active' : '' }}" href="{{ route('get_view_estimate') }}">
          <i class="nav-icon icon-doc"></i> ภาพรวมงบประมาณจริง</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/view/estimate/exp')) ? 'active' : '' }}" href="{{ route('get_view_estimate_export') }}">
          <i class="nav-icon fa fa-download"></i> เสนองบประมาณ</a>
      </li>
      @endif
      {{--@if(Auth::user()->type != 6)--}}
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('view/all')) ? 'active' : '' }}" href="{{ route('get_view') }}">
          <i class="nav-icon icon-check"></i> ขออนุมัติงบประมาณ</a>
      </li>
      {{--@endif--}}
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/status')) ? 'active' : '' }}" href="{{ route('get_status') }}">
          <i class="nav-icon fa fa-bar-chart"></i> ขั้นตอนงบประมาณ</a>
      </li>
      @if(Auth::user()->type ==2 || Auth::user()->type ==3)
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/view/version')) ? 'active' : '' }}" href="{{ route('get_version') }}">
          <i class="nav-icon fa fa-eye"></i> Version งบประมาณ</a>
      </li>
      @endif
      <li class="nav-item nav-dropdown {{ (request()->is('report/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
          <i class="nav-icon fa fa-file"></i> รายงาน</a>
        <ul class="nav-dropdown-items">
          @if(Auth::user()->type != 4)
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('report/compare')) ? 'active' : '' }}" href="{{ route('get_compare') }}">
              <i class="nav-icon fa fa-file"></i> เปรียบเทียบงบประมาณ</a>
          </li>
          @endif
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('report/approve')) ? 'active' : '' }}" href="{{ route('get_approve') }}">
              <i class="nav-icon fa fa-file"></i> สถานะอนุมัติงบประมาณ</a>
          </li>
        </ul>
      </li>
      {{--@if(Auth::user()->type == 5 || Auth::user()->type == 1 || Auth::user()->type == 6)
      <li class="nav-item nav-dropdown {{ (request()->is('sap/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           ระบบ SAP</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('/sap/view/approve')) ? 'active' : '' }}" href="{{ route('get_view_approve') }}">
              <i class="nav-icon icon-check"></i> อนุมัติงบประมาณเข้า SAP</a>
          </li>
          @if(Auth::user()->type != 6)
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('/sap/export/sap')) ? 'active' : '' }}" href="{{ route('get_export') }}">
              <i class="nav-icon fa fa-download"></i> Export File To SAP</a>
          </li>
          @endif
        </ul>
      </li>
      @endif--}}
      @if(Auth::user()->type == 5 || Auth::user()->type == 1 || Auth::user()->type == 6)
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/sap/export/sap')) ? 'active' : '' }}" href="{{ route('get_export') }}">
          <i class="nav-icon fa fa-download"></i> Export File To SAP</a>
      </li>
      @endif
      @if(Auth::user()->type == 5 || Auth::user()->type == 1)
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/shutdown')) ? 'active' : '' }}" href="{{ route('shutdown') }}">
          <i class="nav-icon fa fa-power-off"></i> Shut down</a>
      </li>
      @endif
      @if(Auth::user()->type ==1)
      <li class="nav-item">
        <a class="nav-link" target="_blank" href="{{ route('register') }}" >
          <i class="nav-icon icon-user"></i> Register</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('view_user/*')) ? 'active' : '' }}" href="{{ route('list_user') }}" >
          <i class="nav-icon icon-people"></i> ข้อมูลผู้ใช้ระบบ</a>
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/download/document.pdf') }}" >
          <i class="nav-icon fa fa-book"></i> คู่มือการใช้งาน</a>
      </li>
    </ul>
  </nav>
</div>
