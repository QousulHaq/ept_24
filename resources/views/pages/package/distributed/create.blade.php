@extends('pages.package.base')

@section('sub-content')
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-5">
          <create-distributed-package package-list-url="{{route('back-office.package.distributed.shareable')}}"></create-distributed-package>
        </div>
      </div>
    </div>
  </div>
@endsection
