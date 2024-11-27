@extends('layouts.skeleton')

@section('app')
  <div class="main-wrapper">
    @stack('modal')
    <div class="navbar-bg"></div>
    <nav class="navbar navbar-expand-lg main-navbar">
      @include('partials.app.topnav')
    </nav>
    <div class="main-sidebar">
      @include('partials.app.sidebar')
    </div>

    <!-- Main Content -->
    <div class="main-content">
      @if(session('success') ?? session('status'))
        <div class="alert alert-success alert-dismissible fade show">
          {{ session('success') ?? session('status') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
    </div>
    <footer class="main-footer">
      @include('partials.app.footer')
    </footer>
  </div>
@endsection
