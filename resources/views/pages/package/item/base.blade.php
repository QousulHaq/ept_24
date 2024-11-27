@extends('layouts.app')

@section('title', 'Manage Question')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Question Management</h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
          <a href="{{ route('back-office.package.show', ['package' => $parent->id, 'subpackage' => $package->id]) }}"
             class="btn btn-info"><i class="fa fa-eye"></i> &nbsp; Package {{ $parent->title }}</a>
        </div>
      </div>
      @yield('sub-content')
    </div>
  </section>
@endsection
