<ul class="dropdown-menu">
  @foreach($roles as $key => $role)
    <li class="{{ $activeRole === $role->name ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('back-office.user.index', $role->name) }}">{{ ucfirst($role->name) }}</a>
    </li>
  @endforeach
</ul>
