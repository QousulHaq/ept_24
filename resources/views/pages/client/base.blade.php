@extends('layouts.app')

@section('title', 'OAuth 2 Client Management')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>OAuth 2 Client Management</h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
          @if(request()->route()->getName() === 'back-office.client.index')
            <a href="{{ route('back-office.client.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> &nbsp; Create</a>
          @else
            <a href="{{ route('back-office.client.index') }}" class="btn btn-info"><i class="fa fa-list"></i> &nbsp; List</a>
          @endif
        </div>
      </div>
    </div>
    @yield('sub-content')
  </section>
@endsection
