<header class="app-header navbar">
  <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">
    <img class="navbar-brand-full" src="{{ asset('admin/img/nt.png') }}" width="90" height="45" alt="CoreUI Logo">
    <img class="navbar-brand-minimized" src="{{ asset('admin/img/nt_logo.png') }}" width="40" height="30" alt="CoreUI Logo">
  </a>
  <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
    <span class="navbar-toggler-icon"></span>
  </button>
  <ul class="nav navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" style="padding-right:20px;" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        {{ Auth::user()->name }}
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-header text-center">
          <strong>Account</strong>
        </div>
        <button class="dropdown-item" data-toggle="modal" data-target="{{'#myEdit'}}">
          <i class="fa fa-key"></i> Change password</button>
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
            <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                @csrf
            </form>
          <i class="fa fa-lock"></i> Logout</a>

      </div>
    </li>
  </ul>
</header>

<div class="modal fade" id="{{'myEdit'}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-info" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Change password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('change_pw') }}" method="post">
        @csrf
        <div class="modal-body">
           <div class="tab-pane p-3 active preview" role="tabpanel">
             <div class="mb-3 row">
               <label class="col-sm-4 col-form-label">รหัสผ่านเดิม</label>
                 <div class="col-sm-8">
                   <input class="form-control @error('old') is-invalid @enderror" name="old" type="password" value="{{ old('old') }}">
                 </div>
             </div>
             <div class="mb-3 row">
               <label class="col-sm-4 col-form-label">รหัสผ่านใหม่</label>
               <div class="col-sm-8">
                 <input class="form-control @error('new') is-invalid @enderror" name="new" type="password" value="{{ old('new') }}">
               </div>
             </div>
             <div class="mb-3 row">
               <label class="col-sm-4 col-form-label">รหัสผ่านใหม่ซ้ำ</label>
               <div class="col-sm-8">
                 <input class="form-control @error('renew') is-invalid @enderror" name="renew" type="password" value="{{ old('renew') }}">
                 <input class="form-control" name="emp_id" type="hidden" value="{{ Auth::user()->emp_id }}">
               </div>
             </div>
           </div>
        </div>
       <div class="modal-footer">
         <button type="submit" class="btn btn-info">Save</button>
       </div>
       </form>
     </div>
   </div>
 </div>
