<?php

namespace App\Http\Controllers\BackOffice;

use App\Entities\Media\Attachment;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AttachmentController extends Controller
{

    public function index()
    {
        // view()->share('attachments', Attachment::query()->latest()->paginate());

        // return view('pages.attachment.index');

        $attachments = Attachment::query()->latest()->paginate();

        return Inertia::render('Attachment', compact('attachments'));

    }

}
