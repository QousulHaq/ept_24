<?php

namespace App\Http\Controllers\Api\Client\Section;

use App\Jobs\CBT\Ticking;
use App\Http\Controllers\Controller;
use App\Entities\CBT\Participant\Section;
use App\Jobs\CBT\Section\Item\Attempt\UpdateExistingAttempt;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * @param \App\Entities\CBT\Participant\Section $section
     * @param \App\Entities\CBT\Participant\Section\Item $item
     * @param \App\Entities\CBT\Participant\Section\Item\Attempt $attempt
     * @return mixed
     * @throws \Throwable
     */
    public function attempt(Request $request, Section $section, Section\Item $item, Section\Item\Attempt $attempt)
    {
        $job = new UpdateExistingAttempt($section, $item, $attempt, $request->all());

        dispatch_sync($job);

        return $job;
    }

    public function tick(Section $section, Section\Item $item)
    {
        $job = new Ticking($item);

        $job->onSuccess(fn () => $section->touch());

        $this->dispatchNow($job);

        return $job;
    }
}
