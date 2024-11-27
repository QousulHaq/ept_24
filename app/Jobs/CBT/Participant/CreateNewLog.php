<?php

namespace App\Jobs\CBT\Participant;

use App\Entities\CBT\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CreateNewLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $attributes;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(
        private Participant $participant,
        array $inputs = []
    ) {
        $this->attributes = Validator::make($inputs, [
            'content' => 'required',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ])->validate();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->attributes['created_at'] = now()->format('Y-m-d H:i:s');
        $this->participant->logs()->firstOrCreate(Arr::only($this->attributes, ['created_at', 'update']), $this->attributes);
    }
}
