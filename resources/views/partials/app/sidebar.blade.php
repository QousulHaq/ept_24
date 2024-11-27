<aside id="sidebar-wrapper">
  <div class="sidebar-brand">
    <a href="">{{ env('APP_NAME') }}</a>
  </div>
  <div class="sidebar-brand sidebar-brand-sm">
    <a href="#">{{ strtoupper(substr(env('APP_NAME'), 0, 2)) }}</a>
  </div>
  <ul class="sidebar-menu">
    <li class="menu-header">Dashboard</li>
    <li class="{{ request()->is('back-office/dashboard') ? ' active' : '' }}"><a class="nav-link" href="{{ route('back-office.dashboard') }}"><i class="fas fa-columns"></i> <span>Dashboard</span></a></li>
    @can('attachment.manage')
    <li class="{{ request()->is('back-office/attachment') ? ' active' : '' }}"><a class="nav-link" href="{{ route('back-office.attachment') }}"><i class="fas fa-file"></i> <span>Attachment</span></a></li>
    @endcan
    @if(auth()->user()->can('package.manage'))
      <li class="{{ request()->is('back-office/package*') ? 'active' : '' }}"><a href="{{ route('back-office.package.index') }}"><i class="fas fa-book"></i> <span>Packages</span></a></li>
    @endif
    <li class="menu-header">Exams</li>
      @can('exam.manage')
      <li class="{{ request()->is('back-office/schedule*') ? 'active' : '' }}"><a href="{{ route('back-office.schedule.index') }}"><i class="fas fa-clock"></i> <span>Schedules</span></a></li>
      <li class="{{ request()->is('back-office/monitor*') ? 'active' : '' }}"><a href="{{ route('back-office.monitor.index') }}"><i class="fas fa-eye"></i> <span>Monitoring</span></a></li>
      @endcan
    @canany(['exam.manage', 'exam.result.show'])
      <li class="{{ request()->is('back-office/history*') ? 'active' : '' }}"><a href="{{ route('back-office.history.index') }}"><i class="fas fa-book"></i> <span>History</span></a></li>
    @endcanany
    @if(auth()->user()->can('user.manage'))
      <li class="menu-header">Users</li>
      <li class="dropdown {{ request()->is('back-office/user*') ? ' active' : '' }}">
        <a class="nav-link has-dropdown" href=""><i class="fas fa-users"></i> <span>Users</span></a>
        <x-sidebar.manage-user></x-sidebar.manage-user>
      </li>
    @endif
    @if(auth()->user()->can('client.manage'))
      <li class="{{ request()->is('back-office/client*') ? 'active' : '' }}"><a href="{{ route('back-office.client.index') }}"><i class="fas fa-building"></i> <span>Client</span></a></li>
    @endif
  </ul>
</aside>
