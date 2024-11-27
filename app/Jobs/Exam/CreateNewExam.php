<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use App\Entities\Account\User;
use Jalameta\Support\Bus\BaseJob;
use App\Events\Exam\ExamCreatedOrUpdated;
use App\Http\Requests\Exam\StoreExamRequest;
use Illuminate\Contracts\Support\Responsable;

class CreateNewExam extends BaseJob implements Responsable
{
    /**
     * @var \App\Entities\CBT\Exam|null
     */
    public ?Exam $exam;

    /**
     * @var array
     */
    protected array $attributes;

    /**
     * CreateNewExam constructor.
     *
     * @param \App\Http\Requests\Exam\StoreExamRequest $storeExamRequest
     * @param array $inputs
     */
    public function __construct(StoreExamRequest $storeExamRequest, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->attributes = $storeExamRequest->validated();
    }

    public function boot()
    {
        $this->exam = new Exam();
        $this->onSuccess(fn () => $this->exam->participants()->sync($this->hashToId($this->attributes['participants'])));
        $this->onSuccess(fn () => event(new ExamCreatedOrUpdated($this->exam->fresh(), $this->request->user())));
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        $this->exam->fill(Arr::except($this->attributes, ['participants']));

        return $this->exam->save();
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): Response|\Symfony\Component\HttpFoundation\Response
    {
        return new Response([
            'status' => $this->status,
            'message' => 'Exam created!. please wait for system to generate question for each student.',
            'data' => $this->exam,
        ]);
    }

    protected function hashToId($hashes): array
    {
        $ids = [];

        foreach ($hashes as $key => $hash) {
            $ids[$key] = User::hashToId($hash);
        }

        return $ids;
    }
}
