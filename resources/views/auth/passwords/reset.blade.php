@extends('layouts.auth')

@section('content')
  <div class="card card-primary">
    @include('components.errors')
    <div class="card-header"><h4>Set a New Password</h4></div>

    <div class="card-body">
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->token ?? $token ?? '' }}">
        <input id="email" type="hidden" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
               name="email" tabindex="1" value="{{ old('email', $request->email) }}" autofocus>
        <div class="form-group">
          <label for="password" class="control-label">Password</label>
          <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}"
                 name="password" tabindex="2">
          <div class="invalid-feedback">
            {{ $errors->first('password') }}
          </div>
        </div>
        <div class="form-group">
          <label for="password_confirmation" class="control-label">Confirm Password</label>
          <input id="password_confirmation" type="password"
                 class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}"
                 name="password_confirmation" tabindex="2">
          <div class="invalid-feedback">
            {{ $errors->first('password_confirmation') }}
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
            Set a New Password
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
