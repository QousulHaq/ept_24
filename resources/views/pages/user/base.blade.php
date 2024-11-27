@extends('layouts.app')

@section('title', 'User Management')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>User Management</h1>
    </div>
    <div class="section-body">
      <h2 class="section-title">@yield('section-title', 'List of '.ucfirst($role).' User')</h2>
      @if(request()->route()->getName() === 'back-office.user.index')<p class="section-lead">This page is for managing users.</p> @endif
      <div class="card">
        <div class="card-body">
          @if(request()->route()->getName() === 'back-office.user.index')
            <a href="{{ route('back-office.user.create', request()->route('role')) }}" class="btn btn-info"><i class="fa fa-plus"></i> &nbsp; Create</a>
            <a href="{{ route('back-office.user.import', request()->route('role')) }}" class="btn btn-warning mx-1"><i class="fa fa-paper-plane"></i> &nbsp; Import</a>
          @else
            <a href="{{ route('back-office.user.index', request()->route('role')) }}" class="btn btn-info"><i class="fa fa-list"></i> &nbsp; List</a>
          @endif
        </div>
      </div>
      @yield('sub-content')
    </div>
  </section>
@endsection
