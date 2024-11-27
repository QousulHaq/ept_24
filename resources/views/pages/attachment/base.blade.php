@extends('layouts.app')

@section('title', 'Attachments')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Attachment Files</h1>
    </div>
    <div class="section-body">
      @yield('sub-content')
    </div>
  </section>
@endsection
