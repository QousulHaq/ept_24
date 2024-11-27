<?php

namespace App\Http\Controllers\BackOffice;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $totalStudent = User::query()->whereIs('student')->count();

        $totalFutureExam = Exam::query()->where('scheduled_at', '>', Carbon::now())->count();
        $totalPresentExam = Exam::query()->where('scheduled_at', '<=', Carbon::now())
            ->where(fn (Builder $builder) => $builder
                ->where('ended_at', '>', Carbon::now())
                ->orWhere('ended_at', '=', null))->count();
        $totalPastExam = Exam::query()->where('ended_at', '!=', null)
            ->where('ended_at', '<=', Carbon::now())->count();

        return view('pages.dashboard',
            compact('totalStudent', 'totalFutureExam', 'totalPastExam', 'totalPresentExam')
        );
    }
}
