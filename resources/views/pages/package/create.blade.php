@extends('pages.package.base')

@section('sub-content')
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-5">
          <form action="{{ route('back-office.package.store') }}" method="post">
            @csrf
            <div class="form-group">
              <label for="title">Title</label>
              <input name="title" id="title" type="text" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" id="description" class="form-control" style="height: 60px">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
              <label for="level">Level</label>
              <input type="number" name="level" class="form-control" id="level" min="1" value="{{ old('level') }}">
            </div>
            <button type="submit" class="btn btn-dark">Save</button>
            <button type="reset" class="btn btn-default">Reset</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
