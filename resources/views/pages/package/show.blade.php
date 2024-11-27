@extends('pages.package.base')

@section('sub-content')
  @if($scheduledExams->count() > 0)
    <div class="alert alert-danger">
      Currently, there is {{ $scheduledExams->count() }} active exam(s)
      [{{ $scheduledExams->map->name->implode(', ') }}].
      Editing this package right now will causes anomaly effect while those exam(s) updating it's participants.
    </div>
  @endif
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <h4>Title : </h4>
          <span class="text-danger text-uppercase">{{ $package->title }}</span>
          <br><br><br>
          <small class="text-info"><b>LEVEL : </b> {{ $package->level }}</small>
        </div>
        <div class="col-md-9">
          <h4>Description : </h4>
          {{ $package->description }}
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-header">
          <h4>Subpackages</h4>
        </div>
        <div class="card-body">
          <div class="accordion">
            @each('pages.package.subpackage.accordion', $package->children, 'package')
          </div>
        </div>
      </div>
      @if(!$package->is_encrypted)
        <div class="card">
          <div class="card-header">
            @push('modal')
              <div class="modal fade" id="add-share" tabindex="-1" role="dialog" aria-labelledby="add-share">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Add access to package {{ $package->name }}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <assign-package-to-client package-id='{{ $package->id }}'></assign-package-to-client>
                    </div>
                  </div>
                </div>
              </div>
            @endpush
            <h4>Share</h4>
            <div class="card-header-form">
              <button class="btn btn-success float-right" data-toggle="modal" data-target="#add-share"><i
                  class="fas fa-plus"></i>&nbsp;&nbsp; Access
              </button>
            </div>
          </div>
          <div class="card-body">
            @foreach($package->clients as $client)
              <button class="btn btn-light btn-block btn-sm text-left pl-3 font-weight-bold" data-toggle="modal" data-target="#edit-share-{{ $client->id }}">
                {{ $client->name }}
                <br>
                <small>last sync : {{ $client->client_share->last_sync?->format('Y-m-d H:i:s') ?? 'never' }}</small>
              </button>
              @push('modal')
                <div class="modal fade" id="edit-share-{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="edit-share-{{ $client->id }}">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <detach-package-to-client package-id='{{$package->id}}' :client-id='{{$client->id}}'></detach-package-to-client>
                      </div>
                    </div>
                  </div>
                </div>
              @endpush
            @endforeach
          </div>
        </div>
      @endif
    </div>
    <div class="col-md-9">
      @if(! $package->is_encrypted)
        @includeWhen((! is_null($subpackage ?? null) && ! empty($intros)),
          'pages.package.item.index',
          ['title' => 'Introduction', 'ancestor' => $package,
           'package' => $subpackage, 'items' => $intros, 'simple' => true])

        @includeWhen((! is_null($subpackage ?? null) && $subpackage instanceof \App\Entities\Question\Package),
          'pages.package.item.index',
          ['ancestor' => $package, 'package' => $subpackage, 'items' => $items])
      @else
        <div class="card">
          <div class="card-body">
            <div class="alert alert-dark">Encrypted Package!.</div>
            <div class="alert alert-danger">You can't modify this package because it was transferred from another instance encrypted.</div>
          </div>
        </div>
      @endif
    </div>
  </div>
  @if(is_array($package->note) && count($package->note) > 0)
    <div class="row">
      <div class="col-md-12">
        <div class="card card-danger">
          <div class="card-body">
            @foreach($package->note as $i => $note)
              <div class="card card-warning">
                <div class="card-header"
                     data-toggle="collapse" data-target="#note-section-{{ $i }}"
                     aria-expanded="false" aria-controls="note-section-{{ $i }}">
                  {{ array_key_exists('package', $note) ? $note['package'] : '' }}
                </div>
                <div class="card-body collapse" id="note-section-{{ $i }}">
                  @if(is_array($note['message']))
                    <ul>
                      @foreach($note['message'] as $message)
                        <li>{{ $message }}</li>
                      @endforeach
                    </ul>
                  @else
                    {{ array_key_exists('message', $note) ? $note['message'] : '' }}
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('javascript')

@endpush
