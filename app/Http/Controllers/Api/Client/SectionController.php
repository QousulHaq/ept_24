<?php

namespace App\Http\Controllers\Api\Client;

use App\Jobs\CBT\Ticking;
use App\Entities\CBT\Participant;
use App\Http\Controllers\Controller;
use App\Jobs\CBT\Section\StartSection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionController extends Controller
{
    public function index(Participant $participant)
    {
        return $participant->fresh(['sections']);
    }

    public function show(Participant\Section $section)
    {
        return $section->fresh([
            'items.attachments',
            'items.answers',
            'items.attempts' => fn (HasMany $builder) => $builder
                ->where('attempt_number', $section->getAttribute('attempts')),
        ]);
    }

    public function start(Participant\Section $section)
    {
        $job = new StartSection($section);

        dispatch_sync($job);

        return $job;
    }

    public function tick(Participant\Section $section)
    {
        $job = new Ticking($section);

        $this->dispatchNow($job);

        return $job;
    }
}
