@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Dashboard</h1>
    </div>
    <div class="section-body">
      <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
              <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Total Student</h4>
              </div>
              <div class="card-body">
                {{ $totalStudent }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
              <i class="far fa-newspaper"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Running Exam</h4>
              </div>
              <div class="card-body">
                {{ $totalPresentExam }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
              <i class="far fa-file"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Exam Scheduled/Done</h4>
              </div>
              <div class="card-body">
                {{ $totalFutureExam.'/'.$totalPastExam }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-success">
              <i class="fas fa-circle"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Online Users</h4>
              </div>
              <div class="card-body" id="online-count"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('javascript')
  <script>
    let usersCounter = 0

    const changeUserCounter = () => document.getElementById('online-count').innerText = usersCounter.toString()

    Echo.join('attendance').here((users) => {
      usersCounter = users.length
      changeUserCounter()
    }).joining(() => {
      usersCounter++
      changeUserCounter()
    }).leaving(() => {
      usersCounter--
      changeUserCounter()
    })
  </script>
@endpush
