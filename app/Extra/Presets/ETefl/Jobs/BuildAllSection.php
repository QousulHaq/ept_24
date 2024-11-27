<?php

namespace App\Extra\Presets\ETefl\Jobs;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Entities\CBT\Participant;
use App\Events\Exam\Participant\ParticipantReady;
use App\Extra\Presets\ETefl;
use App\Extra\Presets\ETefl\Jobs\BuildSection\BuildGrammarSection;
use App\Extra\Presets\ETefl\Jobs\BuildSection\BuildListeningSection;
use App\Extra\Presets\ETefl\Jobs\BuildSection\BuildReadingSection;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuildAllSection implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Entities\CBT\Exam
     */
    private Exam $exam;

    /**
     * @var \App\Entities\Account\User
     */
    private User $user;

    public function __construct(Exam $exam, User $user)
    {
        $this->user = $user;
        $this->exam = $exam;
    }

    public function handle(): void
    {
        $participant = $this->getParticipant();

        $this->batch()?->add([
            new BuildListeningSection(
                $this->exam->package->children->firstWhere('config.title', ETefl::SECTION_LISTENING), $participant),
            new BuildGrammarSection(
                $this->exam->package->children->firstWhere('config.title', ETefl::SECTION_GRAMMAR), $participant),
            new BuildReadingSection(
                $this->exam->package->children->firstWhere('config.title', ETefl::SECTION_READING), $participant),
        ]);
    }

    private function getParticipant(): Participant
    {
        if ($this->user->relationLoaded('detail')) {
            return $this->user->detail;
        }

        if ($this->exam->relationLoaded('participants')) {
            return $this->exam->participants->firstWhere('id', $this->user->id)->detail;
        }

        return $this->exam->participants()->where('id', $this->user->id)->first()->detail;
    }
}
