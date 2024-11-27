@extends('layouts.app')

@section('title', 'Schedules')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Exam(s) Schedule</h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
          @if(request()->route()->getName() === 'back-office.schedule.index')
            <a href="{{ route('back-office.schedule.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> &nbsp; Create</a>
          @else
            <a href="{{ route('back-office.schedule.index') }}" class="btn btn-info"><i class="fa fa-list"></i> &nbsp; List</a>
          @endif
        </div>
      </div>
      @yield('sub-content')
    </div>
  </section>
@endsection
