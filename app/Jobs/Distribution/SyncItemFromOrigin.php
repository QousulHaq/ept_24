<?php

namespace App\Jobs\Distribution;

use App\Entities\Classification;
use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use App\Extra\Eloquent\Scopes\RootEntityScope;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncItemFromOrigin implements ShouldQueue, ShouldBeEncrypted
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Package $package;
    private Collection $attachments;

    public function __construct(Package $package)
    {
        $this->package = $package;
        $this->attachments = collect();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \JsonException
     */
    public function handle(): void
    {
        $items = collect($this->package->distribution->connector->getService()->getItems($this->package->id));

        $items->each($this->itemImporter())
            ->tap($this->itemRemover($this->package));

        if ($this->attachments->count() > 0) {
            $batch = $this->batch();
            if ($batch) {
                $batch->add([
                    new DownloadAttachments($this->attachments, $this->package->distribution_options['base_uri']),
                ]);
            } else {
                dispatch_sync(new DownloadAttachments($this->attachments, $this->package->distribution_options['base_uri']));
            }
        }
    }

    private function itemImporter(): \Closure
    {
        return function (array $item) {
            /** @var Package\Item $itemModel */
            $itemModel = Package\Item::query()
                ->withoutGlobalScope(RootEntityScope::class)
                ->where('id', $item['id'])
                ->firstOrNew();

            $itemModel->forceFill(Arr::except($item, ['pivot', 'answers', 'children', 'attachments', 'classifications']));

            $itemModel->saveQuietly();

            if ($itemModel->exists && array_key_exists('pivot', $item) && count($item['pivot']) > 0) {
                DB::table('package_item')->updateOrInsert(Arr::only($item['pivot'], ['package_id', 'item_id']), $item['pivot']);
            }

            if ($itemModel->exists && array_key_exists('classifications', $item) && count($item['classifications']) > 0) {
                collect($item['classifications'])->each($this->classificationImporter($itemModel));
            }

            if ($itemModel->exists && array_key_exists('answers', $item) && count($item['answers']) > 0) {
                collect($item['answers'])
                    ->each($this->answerImporter())
                    ->tap($this->answerRemover($itemModel));
            }

            if ($itemModel->exists && array_key_exists('children', $item) && count($item['children']) > 0) {
                collect($item['children'])
                    ->each($this->itemImporter())
                    ->tap($this->itemRemover($itemModel));
            }

            if ($itemModel->exists && array_key_exists('attachments', $item) && count($item['attachments']) > 0) {
                collect($item['attachments'])
                    ->each($this->attachmentImporter())
                    ->tap($this->attachmentRemover($itemModel));
            }
        };
    }

    private function itemRemover(Package|Package\Item $holder): \Closure
    {
        return static function (Collection $usedItems) use ($holder) {
            if ($holder instanceof Package) {
                $holder->allItems()->whereNotIn('items.id', $usedItems->pluck('id'))->delete();
            } else {
                $holder->children()->whereNotIn('id', $usedItems->pluck('id'))->delete();
            }
        };
    }

    private function answerImporter(): \Closure
    {
        return static function (array $answer) {
            $answerModel = Package\Item\Answer::query()->where('id', $answer['id'])->firstOrNew();

            $answerModel->forceFill($answer);

            $answerModel->saveQuietly();
        };
    }

    private function answerRemover(Package\Item $item): \Closure
    {
        return static fn (Collection $usedAnswers) => $item->answers()
            ->whereNotIn('id', $usedAnswers->pluck('id'))
            ->cursor()
            ->each->delete();
    }

    private function attachmentImporter(): \Closure
    {
        return function (array $attachment) {
            /** @var Attachment $attachmentModel */
            $attachmentModel = Attachment::query()->where('id', $attachment['id'])->firstOrNew();

            $attachmentModel->forceFill(Arr::except($attachment, ['pivot']));

            $attachmentModel->saveQuietly();

            if ($attachmentModel->exists && array_key_exists('pivot', $attachment)) {
                DB::table('attachable')->updateOrInsert(
                    Arr::only($attachment['pivot'], ['attachment_id', 'attachable_id', 'attachable_uuid', 'attachable_type']),
                    $attachment['pivot'],
                );
            }

            $this->attachments->push($attachmentModel);
        };
    }

    private function attachmentRemover(Package\Item $item): \Closure
    {
        return static function (Collection $usedAttachments) use ($item) {
            $item->attachments()->whereNotIn('attachments.id', $usedAttachments->pluck('id'))
                ->get()
                ->each(fn(Attachment $attachment) => $item->attachments()->detach($attachment->id))
                ->each->delete();
        };
    }

    private function classificationImporter(Package\Item $item): \Closure
    {
        return static function (array $classification) use ($item) {
            /** @var Classification $classificationModel */
            $classificationModel = Classification::query()->firstOrCreate(Arr::only($classification, ['type', 'name']), $classification);

            $item->classifications()->syncWithoutDetaching([$classificationModel->id]);
        };
    }
}
