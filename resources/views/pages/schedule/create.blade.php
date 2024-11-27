@extends('pages.schedule.base')

@section('sub-content')
  <exam-form :packages='@json($packages)'
             @success="redirect('{{ route('back-office.schedule.index') }}')"></exam-form>
@endsection
