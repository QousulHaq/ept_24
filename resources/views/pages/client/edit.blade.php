@extends('pages.client.base')

@section('sub-content')
  <passport-form :client='JSON.parse(`@json($client)`)'></passport-form>
@endsection
