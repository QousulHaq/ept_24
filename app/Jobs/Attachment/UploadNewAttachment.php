<?php

namespace App\Jobs\Attachment;

use Illuminate\Http\UploadedFile;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\Media\Attachment;

class UploadNewAttachment extends BaseJob
{
    /**
     * @var \App\Entities\Media\Attachment
     */
    public Attachment $attachment;
    /**
     * @var \Illuminate\Http\UploadedFile
     */
    private UploadedFile $file;

    private string $hash;

    /**
     * UploadNewAttachment constructor.
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $inputs
     * @throws \Exception
     */
    public function __construct(UploadedFile $file, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->request->validate([
            'title' => 'nullable',
            'description' => 'nullable',
            'options' => 'nullable|array',
        ]);

        $this->hash = hash('crc32b', bin2hex(random_bytes(16)));
        $this->file = $file;
        $this->attachment = new Attachment();
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        $this->file->storeAs($this->getUploadDirectory(), $this->getFileNameWithExtension(), 'attachments');
        $this->attachment->fill([
            'user_id' => auth()->user()->id,
            'title' => $this->request->input('title', $this->file->getClientOriginalName()),
            'mime' => $this->file->getMimeType(),
            'path' => $this->getUploadDirectory().'/'.$this->getFileNameWithExtension(),
            'description' => $this->request->input('description'),
            'options' => $this->request->input('options', []),
        ]);

        return  $this->attachment->save();
    }

    /**
     * @return \App\Entities\Media\Attachment
     */
    public function getAttachment(): Attachment
    {
        return $this->attachment;
    }

    /**
     * Get File Name with extension.
     *
     * @return string
     */
    protected function getFileNameWithExtension(): string
    {
        return $this->hash.'.'.$this->file->getClientOriginalExtension();
    }

    /**
     * Get upload directory.
     *
     * @return string
     */
    protected function getUploadDirectory(): string
    {
        return  '/'.substr($this->hash, 0, 2).'/'.substr($this->hash, -2);
    }
}
