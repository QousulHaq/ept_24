<?php

namespace App\Extra\Presets\Sman\Jobs;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Entities\CBT\Participant;
use App\Events\Exam\Participant\ParticipantReady;
use App\Extra\Presets\Sman;
use App\Extra\Presets\Sman\Jobs\BuildSection\BuildKognitifSection;
use App\Extra\Presets\Sman\Jobs\BuildSection\BuildLiterasiSection;
use App\Extra\Presets\Sman\Jobs\BuildSection\BuildPenalaranSection;
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
            new BuildKognitifSection(
                $this->exam->package->children->firstWhere('config.title', Sman::SECTION_KOGNITIF), $participant),
            new BuildPenalaranSection(
                $this->exam->package->children->firstWhere('config.title', Sman::SECTION_PENALARAN), $participant),
            new BuildLiterasiSection(
                $this->exam->package->children->firstWhere('config.title', Sman::SECTION_LITERASI), $participant),
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
