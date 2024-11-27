<?php

namespace App\Console\Commands;

use App\Entities\Media\Attachable;
use App\Entities\Media\Attachment;
use App\Extra\Eloquent\Scopes\RootEntityScope;
use Illuminate\Console\Command;

class AttachableClearOrphaned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachable:clear-orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear orphaned attachable';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->getOutput()->title('Preparing data');

        $this->withProgressBar(
            Attachment::query()
                ->cursor()
                ->map(fn(Attachment $attachment) => $attachment
                    ->attachables()
                    ->cursor()
                    ->reject(fn(Attachable $attachable) => $attachable->attachable_type::query()
                        ->withoutGlobalScopes([RootEntityScope::class])
                        ->find($attachable->attachable_uuid))
                )
                ->flatten(1),
            fn(Attachable $attachable) => $attachable->delete());

        return 0;
    }
}
