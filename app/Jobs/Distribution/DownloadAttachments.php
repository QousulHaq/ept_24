<?php

namespace App\Jobs\Distribution;

use App\Entities\Media\Attachment;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DownloadAttachments implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Collection $attachments,
        public string $url,
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $attachments = $this->attachments->filter(fn(Attachment $attachment) => ! file_exists(config('filesystems.disks.attachments.root').$attachment->path))->values();

        $attachmentUrls = $attachments->map(fn(Attachment $attachment) => $this->url.$attachment->url);

        $responses = Http::pool(fn(Pool $pool) => $attachmentUrls->map(fn($url) => $pool->get($url)));

        $attachments->each(function (Attachment $attachment, int $index) use ($responses) {
            $response = $responses[$index];

            $filePath = config('filesystems.disks.attachments.root').$attachment->path;
            $dir = Str::beforeLast($filePath, '/');

            if (! is_dir($dir) && ! mkdir($dir, recursive: true) && ! is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }

            file_put_contents($filePath, $response->body());
        });
    }
}
