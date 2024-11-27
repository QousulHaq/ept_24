@extends('pages.monitor.base')

@section('sub-content')
  <list-exam state="present" detail-url="{{ route('back-office.monitor.detail', ['exam' => ':id']) }}"></list-exam>
@endsection
