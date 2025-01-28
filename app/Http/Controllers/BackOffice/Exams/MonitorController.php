<?php

namespace App\Http\Controllers\BackOffice\Exams;

use App\Entities\CBT\Exam;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Entities\Account\User;
use Inertia\Inertia;

use App\Entities\CBT\Participant;
use App\Events\Exam\ExamEnded;
use App\Exceptions\Distribution\FailedDecryptSecret;
use App\Extra\Distribution;
use App\Jobs\CBT\Participant\CreateNewLog;
use Illuminate\Http\JsonResponse;
use App\Jobs\Exam\StartAnExam;
use App\Jobs\Exam\QualifyParticipant;
use App\Jobs\Exam\DisqualifyParticipant;
use Illuminate\Http\RedirectResponse;

class MonitorController extends Controller
{
    /**
     * List page of Exam.
     *
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    // public function index()
    // {
    //     return view('pages.monitor.index');
    // }
    
    public function index()
    {
        $query = Exam::query();

        $query->where('scheduled_at', '<=', Carbon::now())->where(fn (Builder $builder) => $builder->where('ended_at', '>', Carbon::now())->orWhereNull('ended_at'));

        $query->latest();
        $monitor = $query->paginate();

        return Inertia::render('Monitor/Index', compact('monitor'));
    }

    /**
     * Detail page of Exam.
     *
     * @param Exam $exam
     */
    // * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    // public function show(Exam $exam)
    // {
    //     return view('pages.monitor.show', [
    //         'exam_id' => $exam->id,
    //     ]);
    // }

    public function show(Request $request, Exam $exam)
    {
        $exam->load($request->query('with', []));
        $exam->participants->each(fn (User $user) => $user->detail->setAppends(['score'])->load('logs'));
        
        return Inertia::render('Monitor/Show', [
            'exam' => $exam->toArray(),
        ]);
    }

    /**
     * @param \App\Entities\CBT\Exam $exam
     * @return \App\Entities\CBT\Exam
     * @throws \Throwable
     */
    public function startExam(Exam $exam): RedirectResponse
    {
        $job = new StartAnExam($exam);
        
        dispatch_sync($job);

        $exam->save();
        
        return redirect()->back()->with('status', 'Exam started.');
    }

    public function endExam(Exam $exam): RedirectResponse
    {
        event(new ExamEnded($exam));

        $exam->save();

        return redirect()->route('back-office.monitor.index')->with('status', 'Exam ended.');
    }

    public function disqualifiedParticipant(Exam $exam, User $user): RedirectResponse
    {
        $job = new DisqualifyParticipant($exam, $user);
        
        dispatch_sync($job);

        $user->save();
        
        return redirect()->back()->with('status', 'Participant has been disqulified.');
    }

    public function qualifiedParticipant(Exam $exam, User $user): RedirectResponse
    {
        $job = new QualifyParticipant($exam, $user);
        
        dispatch_sync($job);

        $user->save();

        return redirect()->back()->with('status', 'Participant status has been changed to qualified.');
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
