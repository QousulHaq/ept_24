<?php

namespace App\Http\Controllers\Api\Client;

use App\Entities\CBT\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Jobs\CBT\EnrollExamForParticipant;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->exams();

        $query->with(['package']);

        switch ($request->input('state')) {
            case 'past': $query->whereNotNull('ended_at'); break;
            case 'running':
                $query->whereNull('ended_at')->where('scheduled_at', '<=', Carbon::now());
                break;
            case 'future':
                $query->whereNull('ended_at')->where('scheduled_at', '>=', Carbon::now());
                break;
        }

        return response()->json($query->paginate());
    }

    /**
     * @param \App\Entities\CBT\Exam $exam
     * @return \Illuminate\Contracts\Support\Responsable
     * @throws \Throwable
     */
    public function enroll(Exam $exam)
    {
        $job = new EnrollExamForParticipant($exam);

        $this->dispatchNow($job);

        return $job;
    }
}
