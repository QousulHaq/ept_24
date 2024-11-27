<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use Illuminate\Http\Response;
use Jalameta\Support\Bus\BaseJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Responsable;

class DeleteExistingExam extends BaseJob implements Responsable, ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Entities\CBT\Exam
     */
    private Exam $exam;

    public function __construct(Exam $exam, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->exam = $exam;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function run(): bool
    {
        return $this->exam->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'data' => $this->exam,
        ]);
    }
}
