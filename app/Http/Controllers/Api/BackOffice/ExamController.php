<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Entities\CBT\Exam;
use App\Entities\CBT\Participant;
use App\Events\Exam\ExamEnded;
use App\Exceptions\Distribution\FailedDecryptSecret;
use App\Extra\Distribution;
use App\Jobs\CBT\Participant\CreateNewLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Entities\Account\User;
use App\Jobs\Exam\StartAnExam;
use Illuminate\Support\Carbon;
use App\Jobs\Exam\CreateNewExam;
use App\Http\Controllers\Controller;
use App\Jobs\Exam\QualifyParticipant;
use App\Jobs\Exam\UpdateExistingExam;
use App\Jobs\Exam\DisqualifyParticipant;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Exam\StoreExamRequest;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Exam::query();

        switch ($request->input('state', 'future')) {
            case 'future':
                $query->where('scheduled_at', '>', Carbon::now());
                break;
            case 'present':
                $query->where('scheduled_at', '<=', Carbon::now())
                    ->where(fn (Builder $builder) => $builder
                        ->where('ended_at', '>', Carbon::now())
                        ->orWhereNull('ended_at'));
                break;
            case 'past':
            default:
                $query->whereNotNull('ended_at')->where('ended_at', '<=', Carbon::now());
                break;
        }

        $query->latest();

        return $query->paginate();
    }

    public function show(Request $request, Exam $exam): Exam
    {
        $exam->load($request->query('with', []));
        $exam->participants->each(fn (User $user) => $user->detail->setAppends(['score'])->load('logs'));

        return $exam;
    }

    public function store(StoreExamRequest $request): Responsable
    {
        $job = new CreateNewExam($request);

        $this->dispatchNow($job);

        return $job;
    }

    /**
     * @param \App\Entities\CBT\Exam $exam
     * @return \App\Entities\CBT\Exam
     * @throws \Throwable
     */
    public function startExam(Exam $exam): Exam
    {
        $job = new StartAnExam($exam);

        $this->dispatchNow($job);

        return $exam;
    }

    public function endExam(Exam $exam): Exam
    {
        event(new ExamEnded($exam));

        return $exam;
    }

    public function disqualifiedParticipant(Exam $exam, User $user): User
    {
        $job = new DisqualifyParticipant($exam, $user);

        $this->dispatchNow($job);

        return $user;
    }

    public function qualifiedParticipant(Exam $exam, User $user): User
    {
        $job = new QualifyParticipant($exam, $user);

        $this->dispatchNow($job);

        return $user;
    }

    public function update(StoreExamRequest $request, Exam $exam): UpdateExistingExam
    {
        $job = new UpdateExistingExam($request, $exam);

        $this->dispatchNow($job);

        return $job;
    }

    /**
     * @throws \Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData
     * @throws FailedDecryptSecret
     */
    public function decrypt(Exam $exam, Distribution $distribution): JsonResponse
    {
        $package = $exam->package;

        if ($package->is_encrypted) {
            // init distribution to trigger cache generator
            $distribution->from($package);
        }

        return response()->json([
            'status' => 'success.',
        ]);
    }

    public function destroy(Exam $exam)
    {
    }

    /**
     * @param Request $request
     * @param Exam $exam
     * @param User $user
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeLog(Request $request, Exam $exam, User $user): JsonResponse
    {
        /** @var Participant $participant */
        $job = new CreateNewLog($participant = $exam->participants()->where('users.id', $user->id)->first()->detail, $request->all());

        $this->dispatchSync($job);

        return response()->json([
            'status' => $participant->load('logs'),
        ]);
    }
}
