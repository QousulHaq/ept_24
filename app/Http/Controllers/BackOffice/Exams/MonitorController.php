<?php

namespace App\Http\Controllers\BackOffice\Exams;

use App\Entities\CBT\Exam;
use App\Http\Controllers\Controller;

class MonitorController extends Controller
{
    /**
     * List page of Exam.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('pages.monitor.index');
    }

    /**
     * Detail page of Exam.
     *
     * @param Exam $exam
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Exam $exam)
    {
        return view('pages.monitor.show', [
            'exam_id' => $exam->id,
        ]);
    }
}
