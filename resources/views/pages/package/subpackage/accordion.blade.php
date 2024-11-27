@if($package->children->count() > 0)
  <div id="accordion">
    <div class="accordion">
      <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-{{ $package->id }}">
        <h4>{{ $package->title }}</h4>
      </div>
      <div class="accordion-body collapse" id="panel-body-{{ $package->id }}">
        <p class="mb-0 ml-1">
          @each('pages.package.subpackage.accordion', $package->children, 'package')
        </p>
      </div>
    </div>
  </div>
  @else
  <h4>
    <a class="btn btn-light btn-block btn-sm text-left pl-3 font-weight-bold"
       href="{{ \Illuminate\Support\Str::finish(url()->current(), '?').\Illuminate\Support\Arr::query(['subpackage' => $package->id]) }}">{{ $package->title }}</a>
  </h4>
@endif
