
<div class="sidebar">
  <nav class="sidebar-nav">
    <ul class="nav">
      <!-- <li class="nav-title">จัดการระบบ</li> -->
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/dashboard')) ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="nav-icon icon-pencil"></i> ฝ่าย-แผนก</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/home')) ? 'active' : '' }}" href="{{ route('home') }}">
          <i class="nav-icon icon-pencil"></i> ศูนย์ต้นทุน</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/event')) ? 'active' : '' }}" href="{{ route('event') }}">
          <i class="nav-icon icon-calendar"></i> ปฏิทิน</a>
      </li>
      <li class="nav-item nav-dropdown {{ (request()->is('budget/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           งบประมาณลงทุนประจำปี</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/add')) ? 'active' : '' }}" href="{{ route('add') }}">
              <i class="nav-icon fa fa-plus"></i> Add</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/edit')) ? 'active' : '' }}" href="{{ route('export') }}">
              <i class="nav-icon icon-pencil"></i> Edit</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/import')) ? 'active' : '' }}" href="{{ route('import') }}">
              <i class="nav-icon icon-doc"></i> import file</a>
          </li>
        </ul>
      </li>
      {{--<li class="nav-item nav-dropdown {{ (request()->is('budget/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           งบประมาณทำการประจำปี</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link " href="">
              <i class="nav-icon fa fa-plus"></i> Add</a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="">
              <i class="nav-icon icon-doc"></i> import file</a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="">
              <i class="nav-icon fa fa-download"></i> export file</a>
          </li>
        </ul>
      </li>--}}
      @if(Auth::user()->type ==1)
      <li class="nav-item">
        <a class="nav-link" target="_blank" href="{{ route('register') }}" >
          <i class="nav-icon icon-people"></i> Register</a>
      </li>
      @endif
    </ul>
  </nav>
</div>
