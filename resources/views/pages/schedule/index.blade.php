@extends('pages.schedule.base')

@section('sub-content')
  <list-exam state="future"
             detail-url="{{ route('back-office.schedule.detail', ['exam' => ':id']) }}"
             edit-url="{{ route('back-office.schedule.edit', ['exam' => ':id']) }}"></list-exam>
@endsection
