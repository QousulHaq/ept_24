@push('modal')
  @if(! ($simple ?? false))
    <div class="modal fade" id="modal-attach-question" tabindex="-1" role="dialog"
         aria-labelledby="modal-attach-question">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <assign-question-to-package :package-id="'{{$subpackage->id}}'" @success="reload()"></assign-question-to-package>
          </div>
        </div>
      </div>
    </div>
  @endif
@endpush

<div class="card">
  <div class="card-header">
    <h4>{{ $title ?? $subpackage->title ?? '' }}</h4>
    <div class="card-header-form">
      @if(! ($simple ?? false))
        <form>
          <div class="input-group">
            <label for="search"></label>
            <input type="hidden" name="subpackage" value="{{ request('subpackage', '') }}">
            <input type="text" id="search" class="form-control" name="keyword" placeholder="Search"
                   value="{{ request('keyword', '') }}">
            <div class="input-group-btn">
              <button class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      @endif
    </div>
  </div>
  <div class="card-body">
    @if(! ($simple ?? false))
      <a
        href="{{ route('back-office.package.item.create', ['package' => $ancestor->id, 'subpackage' => $package->id]) }}"
        class="btn btn-success float-right mb-3"><i class="fas fa-plus"></i>&nbsp;&nbsp; Question
      </a>
      <button
        data-toggle="modal" data-target="#modal-attach-question"
        class="btn mr-3 btn-primary float-right mb-3"><i class="fas fa-lock"></i>&nbsp;&nbsp; Attach Question
      </button>
    @endif
    <div class="table-responsive">
      <table class="table table-striped table-md">
        <thead>
        <tr>
          <th>#</th>
          @if(count(\Illuminate\Support\Arr::get($subpackage->config, 'categories', [])) > 0)
            <th>Category</th>
          @endif
          <th>Code</th>
          <th style="width: 14em">Last Updated</th>
          <th style="width: 14em"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $key => $item)
          <tr>
            <td>{{ ($items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->firstItem() : 1) + $key }}</td>
            @if(count(\Illuminate\Support\Arr::get($subpackage->config, 'categories', [])) > 0)
              <td>{{ $item->category_name }}</td>
            @endif
            <td>{{ $item->code }}</td>
            <td>{{ $item->updated_at->longRelativeToNowDiffForHumans() }}</td>
            <td class="text-right">
              {{--<a href="#" class="mr-4"><i class="fas fa-eye alert-info"></i></a>--}}
              <a
                href="{{ route('back-office.package.item.edit', ['package' => $ancestor->id, 'subpackage' => $subpackage->id, 'item' => $item->id]) }}"
                class="mr-4"><i class="fas fa-edit alert-warning"></i>
              </a>
              @if(! ($simple ?? false))
                <delete-existing-item package="{{ $subpackage->id }}" item="{{ $item->id }}"></delete-existing-item>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center">There is no questions</td>
          </tr>
        @endforelse
        </tbody>
      </table>
      @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {{ $items->appends(request()->only(['subpackage', 'keyword']))->links() }}
      @endif
    </div>
  </div>
</div>
