<?php

namespace App\Jobs\Attachment;

use Jalameta\Support\Bus\BaseJob;
use App\Entities\Media\Attachment;
use Illuminate\Filesystem\Filesystem;

class DeleteExistingAttachment extends BaseJob
{
    /**
     * @var \App\Entities\Media\Attachment
     */
    private Attachment $attachment;

    public function __construct(Attachment $attachment, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->attachment = $attachment;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run(): bool
    {
        /**
         * @var \Illuminate\Filesystem\Filesystem
         */
        $filesystem = app(Filesystem::class);

        $filesystem->delete(storage_path('/app/attachments'.$this->attachment->path));

        return $this->attachment->delete();
    }

    /**
     * @return \App\Entities\Media\Attachment
     */
    public function getAttachment(): Attachment
    {
        return $this->attachment;
    }
}
