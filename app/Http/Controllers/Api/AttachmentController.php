<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\UploadedFile;
use App\Entities\Media\Attachment;
use App\Exceptions\CBT\InvalidFile;
use App\Http\Controllers\Controller;
use App\Jobs\Attachment\UploadNewAttachment;
use App\Jobs\Attachment\DeleteExistingAttachment;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AttachmentController extends Controller
{
    /**
     * Upload file attachment.
     *
     * Route Path       : /attachment
     * Route Method     : POST
     * Route Name       : attachment.store
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(): \Illuminate\Http\JsonResponse
    {
        $file = request()->file('file');

        throw_if($file instanceof UploadedFile === false, new InvalidFile('Invalid File Upload'));
        $job = new UploadNewAttachment($file);
        $this->dispatch($job);

        return response()->json($job->attachment->toArray());
    }

    public function showData(Attachment $attachment)
    {
        return response()->json($attachment->toArray());
    }

    /**
     * Stream an attachment.
     *
     * Route Path       : /attachment/{attachment_uuid}
     * Route Method     : GET
     * Route Name       : attachment.show
     *
     * @param \App\Entities\Media\Attachment $attachment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    public function show(Attachment $attachment): ?\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            return response()->download(storage_path('/app/attachments/'.$attachment->path));
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (FileNotFoundException $e) {
            abort(404);
        }

        return null;
    }

    /**
     * Delete existing attachment.
     *
     * Route Path       : /attachment/{attachment_uuid}
     * Route Method     : DELETE
     * Route Name       : attachment.destroy
     *
     * @param \App\Entities\Media\Attachment $attachment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Attachment $attachment): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->dispatch(new DeleteExistingAttachment($attachment));

        if ($deleted->success()) {
            return response()->json($attachment->toArray(), 200);
        }

        return response()->json(['error' => 'Something went wrong'], 400);
    }

    public function update(Attachment $attachment)
    {
    }
}
