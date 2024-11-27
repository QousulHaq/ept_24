@extends('pages.schedule.base')

@section('sub-content')
  <exam-form :packages='@json($packages)' :exam='@json($exam)'
             @success="redirect('{{ route('back-office.schedule.index') }}')"></exam-form>
@endsection
