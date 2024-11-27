@extends('pages.user.base')

@section('section-title', 'Change Password of '.ucfirst($user->name))

@section('sub-content')
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-5">
          <form action="{{ route('back-office.user.update', [request()->route('role'), $user->hash]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="form-group">
              <label for="password">New password</label>
              <input name="password" id="password" type="password" class="form-control" value="">
            </div>
            <div class="form-group">
              <label for="password_confirmation">Type password again</label>
              <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="">
            </div>
            <button type="submit" class="btn btn-dark">Save</button>
            <button type="reset" class="btn btn-default">Reset</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
