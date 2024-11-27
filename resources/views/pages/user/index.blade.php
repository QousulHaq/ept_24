@extends('pages.user.base')

@section('sub-content')
  <div class="card">
    <div class="card-header">
      <h4>{{ strtoupper($role) }}</h4>
      <div class="card-header-form">
        <form>
          <div class="input-group">
            <label for="search"></label>
            <input type="text" id="search" name="keyword" placeholder="Search" value="{{ request('keyword') }}" class="form-control">
            <div class="input-group-btn">
              <button class="btn btn-primary">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
          <tr>
            <th>#</th>
            <th>ID - Name</th>
            <th>Username</th>
            <th>Email</th>
            <th class="w-25"></th>
          </tr>
          </thead>
          <tbody>
          @forelse($users as $key => $user)
            <tr>
              <td>{{ $users->firstItem() + $key }}</td>
              <td><small class="text-info">{{ $user->alt_id ? $user->alt_id.' - ' : '' }}</small>{{ $user->name }}</td>
              <td>{{ $user->username }}</td>
              <td>{{ $user->email }}</td>
              <td class="align-content-center">
                <a href="{{ route('back-office.user.edit', ['role' => $role, 'user' => $user->hash]) }}" class="btn"><i class="fas fa-edit alert-warning"></i></a>
                @if(auth()->user()->isAn('superuser'))
                  <a href="{{ route('back-office.user.password', ['role' => $role, 'user' => $user->hash]) }}" data-toggle="tooltip" data-placement="left" title="Force change password for user." class="btn"><i class="fas fa-lock alert-danger"></i></a>
                @endif
                @if($role !== 'superuser' && auth()->user()->can('user.manage'))
                  <form method="post" action="{{ route('back-office.user.destroy', ['role' => $role, 'user' => $user->hash]) }}" class="d-inline-block" id="delete-{{ $user->id }}">
                    @csrf
                    @method('delete')
                    <button class="btn" type="button"
                            onclick="swal('are you sure want to delete `{{ $user->name }}` ?', { buttons: ['No !', 'Yes, Please !'], dangerMode:true }).then(a => a ? document.getElementById('delete-{{ $user->id }}').submit() : null)"><i class="fas fa-trash alert-danger"></i></button>
                  </form>
                @endif
                <form method="post" action="{{ route('auth.reset') }}" class="d-inline-block">
                  @csrf
                  <input type="hidden" name="email" value="{{ $user->email }}">
                  <button class="btn btn-warning" data-toggle="tooltip" data-placement="left" title="Send email for reset password to user" type="submit">Reset Password</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5">There is no {{ request()->route('role') }} users.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
        <div class="ml-4">{{ $users }}</div>
      </div>
    </div>
  </div>
@endsection
