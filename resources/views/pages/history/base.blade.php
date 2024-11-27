@extends('layouts.app')

@section('title', 'Result')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Exam(s) Result</h1>
    </div>
    <div class="section-body">
      @yield('sub-content')
    </div>
  </section>
@endsection
