@extends('pages.history.base')

@push('modal')
  @foreach($exam->participants as $participant)
    <div class="modal fade" id="edit-score-{{ $participant->id }}" tabindex="-1" role="dialog"
         aria-labelledby="edit-score-{{ $participant->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Score</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="{{ route('back-office.history.update-score', ['exam' => $exam->id, 'participant' => $participant->detail->id]) }}">
            @csrf
            <div class="modal-body">
              @foreach($participant->detail->sections as $key => $section)
                <input type="hidden" name="scores[{{ $key }}][id]" value="{{ $section->id }}">
                <div class="form-group">
                  <label for="score-{{ $section->id }}">{{ $section->config['title'] }}</label>
                  <input name="scores[{{ $key }}][value]" id="score-{{ $section->id }}" type="number" max="68" class="form-control" value="{{ round($section->score) }}">
                </div>
              @endforeach
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
@endpush

@section('sub-content')
  <div>

    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <a class="btn btn-info text-white" href="{{ route('back-office.history.index') }}"><i class="fa fa-list"></i>
            &nbsp; List
          </a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <h4>Name:</h4>
            <span class="text-uppercase font-weight-bold text-danger">{{ $exam->name }}</span>
          </div>
          <div class="col-md-3">
            <h4>Scheduled:</h4>
            <span>{{ $exam->scheduled_at ?? '-' }}</span>
          </div>
          <div class="col-md-3">
            <h4>Ended:</h4>
            <span>{{ $exam->ended_at ?? '-' }}</span>
          </div>
        </div>
      </div>
    </div>

    <h1 style="color: #34395e;font-size: 24px;font-weight: 700;padding: 20px">Participants</h1>
    <div class="card">
      <div class="card-body table-responsive p-0">
        <table class="table table-bordered">
          <thead class="text-center">
          <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">ID</th>
            <th rowspan="2">Name</th>
            <th colspan="{{ $sectionCount = $exam->participants->first()?->detail?->sections?->count() ?: 1 }}"
                rowspan="{{ $sectionCount == 0 ? 2 : 1 }}">
              Scores
            </th>
            <th colspan="2">Total Score</th>
            <th rowspan="2"></th>
          </tr>
            <tr>
              @forelse(($exam->participants->first()?->detail?->sections ?? []) as $section)
                <th>{{ Arr::get($section, 'config.title') }}</th>
                @empty
                <th></th>
              @endforelse
              <th>Total</th>
              <th>Final Score</th>
            </tr>
          </thead>
          <tbody>
          @foreach($exam->participants->sortBy('name')->values() as $key => $participant)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ $participant->alt_id ?? '-' }}</td>
              <td>{{ $participant->name }}</td>
              @forelse($participant->detail->sections as $section)
                <td class="text-center">{{ round($section->score) }}</td>
                @empty
                <td></td>
              @endforelse
              <td class="text-center">{{ $participant->detail->sections->sum('score') }}</td>
              <td class="text-center">{{ round($participant->detail->score) }}</td>
              <td>
                <button class="btn btn-warning btn-block my-1" data-toggle="modal" data-target="#edit-score-{{ $participant->id }}">Edit Score</button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@push('javascript')
  <script type="text/javascript">
    $('#edit-score').click(function () {
      console.log('test')
    })
  </script>
@endpush
