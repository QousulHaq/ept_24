@extends('pages.history.base')

@section('sub-content')
  <list-exam state="past" detail-url="{{ route('back-office.history.detail', ['exam' => ':id']) }}"></list-exam>
@endsection
