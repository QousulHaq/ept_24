<form class="form-inline mr-auto" action="">
  <ul class="navbar-nav mr-3">
    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars tw-text-primary3"></i></a></li>
  </ul>
</form>
<ul class="navbar-nav navbar-right tw-mt-0">
  <li-notification></li-notification>
  <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
      <div class="d-sm-none d-lg-inline-block tw-text-black">Hi, {{ auth()->user()->name }}</div>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
      <div class="dropdown-title">Welcome, {{ auth()->user()->name }}</div>
      <a href="#" class="dropdown-item has-icon">
        <i class="far fa-user"></i> Profile Settings
      </a>
      <div class="dropdown-divider"></div>
      <a href="#" class="dropdown-item has-icon text-danger"
        onclick="window.$axios.post('{{ url('auth/logout') }}').then(() => window.location.href = '/')">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </li>
</ul>