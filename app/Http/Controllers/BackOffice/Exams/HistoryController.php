<?php

namespace App\Http\Controllers\BackOffice\Exams;

use App\Entities\CBT\Exam;
use App\Entities\CBT\Participant;
use App\Http\Controllers\Controller;
use App\Jobs\Section\UpdateSectionScore;
use Illuminate\Http\RedirectResponse;

class HistoryController extends Controller
{
    public function index()
    {
        return view('pages.history.index');
    }

    /**
     * Detail page of Exam History.
     *
     * @param Exam $exam
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Exam $exam)
    {
        return view('pages.history.show', [
            'exam' => $exam,
        ]);
    }

    public function updateScore(Exam $exam, Participant $participant): RedirectResponse
    {
        $job = new UpdateSectionScore($participant);

        $this->dispatchNow($job);

        return ($job->success())
            ? redirect()->route('back-office.history.detail', ['exam' => $exam->id])
            : redirect()->back()->withInput()->withErrors(['Internal server error.']);
    }
}
