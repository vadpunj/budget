
<div class="sidebar">
  <nav class="sidebar-nav">
    <ul class="nav">
      <!-- <li class="nav-title">จัดการระบบ</li> -->
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/dashboard')) ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="nav-icon icon-pencil"></i> ฝ่าย-แผนก</a>
      </li>
      {{--<li class="nav-item">
        <a class="nav-link {{ (request()->is('/home')) ? 'active' : '' }}" href="{{ route('home') }}">
          <i class="nav-icon icon-pencil"></i> ศูนย์ต้นทุน</a>
      </li>--}}
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('/event')) ? 'active' : '' }}" href="{{ route('event') }}">
          <i class="nav-icon icon-calendar"></i> ปฏิทิน</a>
      </li>
      <li class="nav-item nav-dropdown {{ (request()->is('budget/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           งบประมาณลงทุนประจำปี</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/add')) ? 'active' : '' }}" href="{{ route('add_bud') }}">
              <i class="nav-icon fa fa-plus"></i> Add Budget</a>
          </li>
          {{--<li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/edit')) ? 'active' : '' }}" href="{{ route('export') }}">
              <i class="nav-icon icon-pencil"></i> Edit</a>
          </li>--}}
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('budget/import')) ? 'active' : '' }}" href="{{ route('import_bud') }}">
              <i class="nav-icon icon-doc"></i> import file</a>
          </li>
        </ul>
      </li>
      <li class="nav-item nav-dropdown {{ (request()->is('estimate/*')) ? 'show open' : '' }}">
        <a class="nav-link nav-dropdown-toggle" href="#">
           งบประมาณทำการประจำปี</a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/add')) ? 'active' : '' }}" href="{{ route('add_est') }}">
              <i class="nav-icon fa fa-plus"></i> Add Estimate</a>
          </li>
          {{--<li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/edit')) ? 'active' : '' }}" href="{{ route('export') }}">
              <i class="nav-icon icon-pencil"></i> Edit</a>
          </li>--}}

          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/add/master')) ? 'active' : '' }}" href="{{ route('add_master') }}">
              <i class="nav-icon fa fa-plus"></i> Add Master</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/import/estimate')) ? 'active' : '' }}" href="{{ route('import_estimate') }}">
              <i class="nav-icon icon-doc"></i> Import Estimate</a>
          </li>
          @if(Auth::user()->type ==1)
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/master')) ? 'active' : '' }}" href="{{ route('import_master') }}">
              <i class="nav-icon icon-doc"></i> Import Master</a>
          </li>
          {{--<li class="nav-item">
            <a class="nav-link {{ (request()->is('estimate/import')) ? 'active' : '' }}" href="{{ route('import') }}">
              <i class="nav-icon icon-doc"></i> Import file</a>
          </li>--}}
          @endif
          <li class="nav-item">
            <a class="nav-link {{ (request()->is('view/all')) ? 'active' : '' }}" href="{{ route('get_view') }}">
              <i class="nav-icon icon-eye"></i> View All</a>
          </li>
        </ul>
      </li>
      @if(Auth::user()->type ==1)
      <li class="nav-item">
        <a class="nav-link" target="_blank" href="{{ route('register') }}" >
          <i class="nav-icon icon-people"></i> Register</a>
      </li>
      @endif
    </ul>
  </nav>
</div>
