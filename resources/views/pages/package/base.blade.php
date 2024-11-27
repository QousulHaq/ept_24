@extends('layouts.app')

@section('title', 'Manage Package')

@push('modal')
  <div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <a href="{{ route('back-office.package.create') }}" class="btn btn-info btn-block"> &nbsp; Create from scratch</a>
          <a href="{{ route('back-office.package.distributed.create') }}" class="btn btn-outline-warning btn-block"> &nbsp; Get distributed package</a>
        </div>
      </div>
    </div>
  </div>
@endpush

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Package Management</h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
          @if(request()->route()->getName() === 'back-office.package.index')
            <button class="btn btn-info" data-toggle="modal" data-target="#create-modal"><i class="fa fa-plus"></i> &nbsp; Create</button>
            @else
            <a href="{{ route('back-office.package.index') }}" class="btn btn-info"><i class="fa fa-list"></i> &nbsp; List</a>
          @endif
        </div>
      </div>
      @yield('sub-content')
    </div>
  </section>
@endsection
