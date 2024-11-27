@extends('pages.package.item.base')

@section('sub-content')
  <create-new-item :config='JSON.parse(`@json(\Illuminate\Support\Arr::get($package->config, 'item', []))`)'
                   :title="`Create Question for {{ \Illuminate\Support\Arr::get($package->config, 'title', '') }} section`.toUpperCase()"
                   id="{{ $package->id }}"
                   @success="redirect('{{ route('back-office.package.show', ['package' => $parent->id, 'subpackage' => $package->id]) }}')">
  </create-new-item>
@endsection
