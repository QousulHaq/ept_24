<aside id="sidebar-wrapper" class="">
  <div class="sidebar-brand tw-p-5">
    {{-- <a href="">{{ env('APP_NAME') }}</a> --}}
    <img src="{{asset('assets\img\logo.svg')}}" alt="" class="tw-ml-5">
  </div>

  <div class="sidebar-brand sidebar-brand-sm">
    <a href="#">{{ strtoupper(substr(env('APP_NAME'), 0, 2)) }}</a>
  </div>

  <ul class="sidebar-menu">

    <li class="tw-p-0 tw-pr-2">
      <a class="nav-link" href="{{ route('back-office.dashboard') }}" style="padding-left: 0; padding-right:0">
        <div class="nav-link-decoration {{ request()->is('back-office/dashboard') ? 'tw-bg-primary3' : 'tw-bg-white' }} tw-h-full tw-p-1.5 tw-mr-2 tw-rounded-r-md"></div>
        <div class="{{ request()->is('back-office/dashboard') ? 'tw-bg-primary3 tw-text-white' : 'tw-bg-white tw-text-black' }} tw-h-full tw-w-full tw-p-3 tw-rounded-md tw-flex tw-justify-center tw-items-center">
          <i class="fas fa-columns"></i><span style="margin-top: 0; font-weight: 400; font-size: small;">Home</span>
        </div>
      </a>
    </li>

    @if(auth()->user()->can('package.manage'))
      <li class="tw-p-0 tw-pr-2">
        <a class="nav-link" href="{{ route('back-office.package.index') }}" style="padding-left: 0; padding-right:0">
          <div class="nav-link-decoration {{ request()->is('back-office/package*') ? 'tw-bg-primary3' : 'tw-bg-white' }} tw-h-full tw-p-1.5 tw-mr-2 tw-rounded-r-md"></div>
          <div class="{{ request()->is('back-office/package*') ? 'tw-bg-primary3 tw-text-white' : 'tw-bg-white tw-text-black' }} tw-h-full tw-w-full tw-p-3 tw-rounded-md tw-flex tw-justify-center tw-items-center">
            <i class="fas fa-book"></i><span style="margin-top: 0; font-weight: 400; font-size: small;">My Bank Questions</span>
          </div>
        </a>
      </li>
    @endif

    @can('attachment.manage')
      <li class="tw-p-0 tw-pr-2">
        <a class="nav-link" href="{{ route('back-office.attachment') }}" style="padding-left: 0; padding-right:0">
          <div class="nav-link-decoration {{ request()->is('back-office/attachment') ? 'tw-bg-primary3' : 'tw-bg-white' }} tw-h-full tw-p-1.5 tw-mr-2 tw-rounded-r-md"></div>
          <div class="{{ request()->is('back-office/attachment') ? 'tw-bg-primary3 tw-text-white' : 'tw-bg-white tw-text-black' }} tw-h-full tw-w-full tw-p-3 tw-rounded-md tw-flex tw-justify-center tw-items-center">
            <i class="fas fa-file"></i><span style="margin-top: 0; font-weight: 400; font-size: small;">Attachment</span>
          </div>
        </a>
      </li>
    @endcan

    @can('exam.manage')
      <li class="tw-p-0 tw-pr-2">
        <a class="nav-link" href="{{ route('back-office.schedule.index') }}" style="padding-left: 0; padding-right:0">
          <div class="nav-link-decoration {{ request()->is('back-office/schedule*') ? 'tw-bg-primary3' : 'tw-bg-white' }} tw-h-full tw-p-1.5 tw-mr-2 tw-rounded-r-md"></div>
          <div class="{{ request()->is('back-office/schedule*') ? 'tw-bg-primary3 tw-text-white' : 'tw-bg-white tw-text-black' }} tw-h-full tw-w-full tw-p-3 tw-rounded-md tw-flex tw-justify-center tw-items-center">
            <i class="fas fa-clock"></i><span style="margin-top: 0; font-weight: 400; font-size: small;">Schedules</span>
          </div>
        </a>
      </li>

      <li class="tw-p-0 tw-pr-2">
        <a class="nav-link" href="{{ route('back-office.monitor.index') }}" style="padding-left: 0; padding-right:0">
          <div class="nav-link-decoration {{ request()->is('back-office/monitor*') ? 'tw-bg-primary3' : 'tw-bg-white' }} tw-h-full tw-p-1.5 tw-mr-2 tw-rounded-r-md"></div>
          <div class="{{ request()->is('back-office/monitor*') ? 'tw-bg-primary3 tw-text-white' : 'tw-bg-white tw-text-black' }} tw-h-full tw-w-full tw-p-3 tw-rounded-md tw-flex tw-justify-center tw-items-center">
            <i class="fas fa-eye"></i><span style="margin-top: 0; font-weight: 400; font-size: small;">Monitoring</span>
          </div>
        </a>
      </li>
    @endcan
    
    {{-- <li class="menu-header">Exams</li>
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
    @endif --}}
  </ul>
</aside>
