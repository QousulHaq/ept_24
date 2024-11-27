<?php

namespace App\Jobs\Section;

use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;

class UpdateSectionScore extends BaseJob
{
    /**
     * @var \App\Entities\CBT\Participant
     */
    public $participant;

    public function __construct(Participant $participant, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->participant = $participant;

        $this->inputs = $this->validate($this->request, [
            'scores.*.id' => 'required|exists:participant_sections,id',
            'scores.*.value' => 'required|int|max:68',
        ]);
    }

    public function run(): bool
    {
        foreach ($this->inputs['scores'] as $score) {
            $section = Participant\Section::query()->findOrFail($score['id']);

            if ($section->participant instanceof $this->participant) {
                $section->fill(['score' => $score['value']]);

                $section->save();
            } else {
                return false;
            }
        }

        return true;
    }
}
