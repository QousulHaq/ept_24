@extends('pages.user.base')

@section('section-title', 'Edit user :'.$user->name)

@section('sub-content')
  <div class="row">
    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
          <form action="{{ route('back-office.user.update', [request()->route('role'), $user->hash]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="form-group">
              <label for="name">Name</label>
              <input name="name" id="name" type="text" class="form-control" value="{{ old('name', $user->name) }}">
            </div>
            <div class="form-group">
              <label for="username">Username</label>
              <input name="username" id="username" type="text" class="form-control" value="{{ old('username', $user->username) }}">
            </div>
            <div class="form-group">
              <label for="alt_id">Identity Number</label>
              <input name="alt_id" id="alt_id" type="text" class="form-control" value="{{ old('alt_id', $user->alt_id) }}">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input name="email" id="email" type="email" class="form-control" value="{{ old('email', $user->email) }}">
            </div>
            <image-form image="{{ optional($image_user)->id  }}"></image-form>
            <div class="form-group">
              <label for="roles">Roles</label>
              <select name="roles[]" id="roles" class="form-control" multiple style="height: 10em">
                @foreach($available_roles as $role)
                  <option value="{{ $role->name }}" {{ $user->isA($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-dark">Save</button>
            <button type="reset" class="btn btn-default">Reset</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-lightbulb"></i>&nbsp; SUGGESTION | HELP
        </div>
        <div class="card-body">
          <div class="alert alert-info">
            <b>Identity Number : </b> can be NRP / NIP / NIK
          </div>
          <div class="alert alert-info">
            <b>Roles : </b>
            <ol>
              <li>better ignore it.</li>
              <li>to select multiple role use <b>ctrl + click <small>(in selected role)</small></b>.</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
