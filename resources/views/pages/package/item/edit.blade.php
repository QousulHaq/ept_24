@extends('pages.package.item.base')

@section('sub-content')
  <edit-existing-item :config='JSON.parse(`@json(\Illuminate\Support\Arr::get($package->config, $isIntro ? 'intro' : 'item', []))`)'
                      package-id="{{ $package->id }}"
                      item-id="{{ $item->id }}"
                      :title="`Edit Question #{{ $item->code }}`.toUpperCase()"
                      :editable-code="{{ $isIntro ?? false ? 'false' : 'true' }}"
                      @success="reload()">
  </edit-existing-item>
@endsection
