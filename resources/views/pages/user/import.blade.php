@extends('pages.user.base')

@section('section-title', 'Create new user')

@section('sub-content')
  <div class="row">
    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
          <form action="{{ route('back-office.user.store-import', $role) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="file">CSV File</label>
              <input type="file" class="form-control" id="file" name="file" placeholder="CSV File" value="{{ old('file') }}">
            </div>
            <div class="form-group">
              <label for="roles">Roles</label>
              <select name="roles[]" id="roles" class="form-control" multiple="multiple" style="height: 10em">
                @foreach($available_roles as $role)
                  <option value="{{ $role->name }}" {{ $role->name === request()->route('role') ? 'selected' : '' }}>{{ $role->name }}</option>
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
          <i class="fa fa-lightbulb"></i>&nbsp; SUGGESTION
        </div>
        <div class="card-body">
          @if(! empty($example_link))
          <div class="alert alert-danger">
            Download the <a href="{{ $example_link }}"><u>sample csv file</u></a>
          </div>
          @endif
          <div class="alert alert-danger">
            Consider to import small number of users at a time. This will reduce the time of the import process.
            Recommendation: 10 users per import.
          </div>
          <div class="alert alert-info">
            <b>Password : </b>
            <ol>
              <li>Password will be generated randomly for each user.</li>
              <li>Password reset link will be send to user after import process success.</li>
            </ol>
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
