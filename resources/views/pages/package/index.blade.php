@extends('pages.package.base')

@push('modal')
  @foreach($packages as $package)
    <div class="modal fade" id="package-delete-{{ $package->id }}" tabindex="-1" role="dialog"
         aria-labelledby="package-delete-{{ $package->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete <span class="text-danger">{{ $package->name }}</span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are u sure want to delete this package ?. This action cannot be undone!.
          </div>
          <div class="modal-footer">
            <form action="{{ route('back-office.package.destroy', ['package' => $package->id]) }}" method="post">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endpush

@section('sub-content')
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Level</th>
            <th>Encrypted</th>
            <th>Last Updated</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          @forelse($packages as $key => $package)
            <tr>
              <td>{{ $packages->firstItem() + $key }}</td>
              <td>{{ $package->title }}</td>
              <td>{{ \Illuminate\Support\Str::limit($package->description, 50) }}</td>
              <td>{{ $package->level }}</td>
              <td><i class="fas {{ $package->is_encrypted ? 'fa-check' : 'fa-times' }}"></i></td>
              <td>{{ $package->updated_at->longRelativeToNowDiffForHumans() }}</td>
              <td class="align-content-center">
                <a href="{{ route('back-office.package.show', ['package' => $package->id]) }}" class="mr-4"><i class="fas fa-eye alert-info"></i></a>
                <a href="{{ route('back-office.package.edit', ['package' => $package->id]) }}" class="mr-4"><i class="fas fa-edit alert-warning"></i></a>
                <button type="button" data-toggle="modal" data-target="#package-delete-{{ $package->id }}" class="btn btn-sm mr-4"><i class="fas fa-trash alert-danger"></i></button>
              </td>
            </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">There is no packages</td>
              </tr>
          @endforelse
          </tbody>
        </table>
        <div class="ml-4">{{ $packages }}</div>
      </div>
    </div>
  </div>
@endsection
