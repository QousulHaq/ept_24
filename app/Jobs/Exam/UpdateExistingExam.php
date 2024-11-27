<?php

namespace App\Jobs\Exam;

use App\Entities\Account\User;
use App\Entities\CBT\Exam;
use Illuminate\Http\Response;
use App\Events\Exam\ExamCreatedOrUpdated;
use App\Http\Requests\Exam\StoreExamRequest;

class UpdateExistingExam extends CreateNewExam
{
    /**
     * UpdateExistingExam constructor.
     *
     * @param \App\Http\Requests\Exam\StoreExamRequest $request
     * @param \App\Entities\CBT\Exam $exam
     * @param array $inputs
     */
    public function __construct(StoreExamRequest $request, Exam $exam, array $inputs = [])
    {
        parent::__construct($request, $inputs);

        $this->exam = $exam;
    }

    public function boot()
    {
        $this->onSuccess(function () {
            // clean up old participant and their sections
            // it will make cost for resource more high but
            // it will make sure all user have same package
            // if package updated during scheduled exam
            $this->exam->participants->each(fn (User $user) => $user->detail->delete());
            // regenerate requested participant(s)
            $this->exam->participants()->sync($this->hashToId($this->attributes['participants']));
        });
        $this->onSuccess(
            fn () => event(new ExamCreatedOrUpdated($this->exam->fresh(),
                $this->request->user(), ExamCreatedOrUpdated::UPDATED)));
    }

    public function run(): bool
    {
        parent::run();

        return true;
    }

    public function toResponse($request): Response|\Symfony\Component\HttpFoundation\Response
    {
        return new Response([
            'status' => $this->status,
            'message' => 'Exam updated !.',
            'data' => $this->exam,
        ]);
    }
}
