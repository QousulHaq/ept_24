<?php

use App\Entities\CBT\Participant;
use Illuminate\Database\Eloquent\Collection;
use Jalameta\Patcher\Patch;

class FixDoubleSections extends Patch
{
    /**
     * Run patch script.
     *
     * @return void
     */
    public function patch()
    {
        Participant::query()->withCount('sections')->cursor()
            ->where('sections_count', '>', 3)->each(function (Participant $participant) {
                $participant->sections->groupBy('config.config')->each(function (Collection $collection) {
                    if ($collection->count() > 1) {
                        $section = $collection->first();
                        $this->command->info('section : '.$section->label.'('.$section->id.') deleted :'.$section->delete());
                    }
                });
            });
    }
}
