@extends('layouts.app')

@section('title', 'Monitoring')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Exam(s) Monitoring</h1>
    </div>
    <div class="section-body">
      @yield('sub-content')
    </div>
  </section>
@endsection

