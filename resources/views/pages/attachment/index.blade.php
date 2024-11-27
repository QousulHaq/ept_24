@extends('pages.attachment.base')

@push('modal')
  @foreach($attachments as $attachment)
    <div class="modal fade" id="attachment-show-{{ $attachment->id }}" tabindex="-1" role="dialog"
         aria-labelledby="attachment-show-{{ $attachment->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="attachmentShowLabel">Attachment "{{ $attachment->title }}"</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body d-flex flex-column">
            <audio class="align-self-center" src="{{ $attachment->url }}" controls></audio>
            <span class="align-self-center pt-4">Used By: {{ $attachment->usedBy }} Items</span>
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
        <table class="table table-stripped table-md">
          <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Mime</th>
            <th class="text-center">Description</th>
            <th class="text-center">Action</th>
          </tr>
          </thead>
          <tbody>
          @foreach($attachments as $key => $attachment)
            <tr>
              <td>{{ $attachments->firstItem() + $key }}</td>
              <td>{{ $attachment->title }}</td>
              <td>{{ $attachment->mime }}</td>
              <td class="text-center">{{ $attachment->description ?: '-' }}</td>
              <td class="align-content-center">
                <button type="button" data-toggle="modal" data-target="#attachment-show-{{ $attachment->id }}"
                        class="btn btn-sm mr-4"><i class="fas fa-eye alert-primary"></i></button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer">
      {{ $attachments->links() }}
    </div>
  </div>
@endsection
