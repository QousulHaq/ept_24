<?php

namespace App\Http\Controllers\BackOffice\Exams;

use App\Entities\CBT\Exam;
use Illuminate\Http\Request;
use App\Entities\Account\User;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

use App\Jobs\Exam\UpdateExistingExam;
use App\Jobs\Exam\CreateNewExam;
use App\Http\Requests\Exam\StoreExamRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, \Closure $next) {
            $packages = Package::query()->without(['children'])->get();
            $users = User::whereIs('student')->get();

            view()->share('packages', $packages);
            view()->share('users', $users);

            return $next($request);
        })->only(['create', 'edit']);
    }

    // public function index()
    // {
    //     return view('pages.schedule.index');
    // }
    public function index()
    {
        $query = Exam::query();
        $query->where('scheduled_at', '>', Carbon::now());

        $query->latest();

        $schedule = $query->paginate();
        return Inertia::render('Schedule/Index', compact('schedule'));
    }

    public function create()
    {
        // return view('pages.schedule.create');

        $participants = User::whereIs('student');

        $packages = Package::query()->without(['children'])->get();

        return Inertia::render('Schedule/Create', [
            'packages' => $packages,
            'participants' => $participants->paginate(),
        ]);
    }

    public function store(StoreExamRequest $request): RedirectResponse
    {
        $job = new CreateNewExam($request);

        dispatch_sync($job);

        return redirect()->route('back-office.schedule.index')->with('status', 'data created.');
    }

    // public function show(Exam $exam)
    // {
    //     return view('pages.schedule.show', [
    //         'exam_id' => $exam->id,
    //     ]);
    // }

    public function show(Request $request, Exam $exam)
    {
        $exam->load($request->query('with', []));
        $exam->participants->each(fn (User $user) => $user->detail->setAppends(['score'])->load('logs'));
        
        return Inertia::render('Schedule/Show', [
            'exam' => $exam->toArray(),
        ]);
    }

    public function edit(Exam $exam)
    {
        // return view('pages.schedule.edit', [
        //     'exam' => $exam->fresh(['participants'])->toArray(),
        // ]);

        $participants = User::whereIs('student');

        $packages = Package::query()->without(['children'])->get();

        return Inertia::render('Schedule/Edit', [
            'exam' => $exam->fresh(['participants'])->toArray(),
            'packages' => $packages,
            'participants' => $participants->paginate(),
        ]);
    }

    public function update(StoreExamRequest $request, Exam $exam): RedirectResponse
    {
        $job = new UpdateExistingExam($request, $exam);

        dispatch_sync($job);

        return redirect()->route('back-office.schedule.index')->with('status', 'data updated.');
    }

    public function participant(Request $request): RedirectResponse
    {
        $query = User::whereIs('student');
        $query->when($request->input('keyword', null), fn (Builder $builder, $value) => $builder->search($value));
        $query->latest();

        dd($query->paginate());

        return redirect()->back()->with('new_participants', $query->paginate());
    }
}
