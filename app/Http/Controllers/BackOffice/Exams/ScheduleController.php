<?php

namespace App\Http\Controllers\BackOffice\Exams;

use App\Entities\CBT\Exam;
use Illuminate\Http\Request;
use App\Entities\Account\User;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;

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

    public function index()
    {
        return view('pages.schedule.index');
    }

    public function create()
    {
        return view('pages.schedule.create');
    }

    public function show(Exam $exam)
    {
        return view('pages.schedule.show', [
            'exam_id' => $exam->id,
        ]);
    }

    public function edit(Exam $exam)
    {
        return view('pages.schedule.edit', [
            'exam' => $exam->fresh(['participants'])->toArray(),
        ]);
    }
}
