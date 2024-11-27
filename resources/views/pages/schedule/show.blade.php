@extends('pages.schedule.base')

@section('sub-content')
  <detail-exam exam-id="{{ $exam_id }}" :no-header="true"></detail-exam>
@endsection
