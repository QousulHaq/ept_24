<?php

namespace App\Extra\Eloquent\Concerns;

use Illuminate\Support\Arr;
use App\Entities\Media\Attachment;
use App\Entities\Media\Attachable as AttachableModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Attachable
{
    public function attachments(): MorphToMany
    {
        return $this->morphToMany(
            Attachment::class,
            'attachable',
            'attachable',
            ($this->keyType === 'string') ? 'attachable_uuid' : 'attachable_id')
            ->using(AttachableModel::class)
            ->withPivot(['order'])
            ->orderBy('attachable.order');
    }

    public function getAttachmentAttribute()
    {
        return Arr::get($this->attachments()->first(), 'id');
    }
}
