<?php

namespace App\Extra\Presets\Cpns\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Package\PackageCreatedOrUpdated;
use App\Extra\Presets\Cpns\Jobs\Package\BuildNoteForPackage;

class CheckPackageRequirements implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PackageCreatedOrUpdated $event)
    {
        dispatch(new BuildNoteForPackage($event->package->ancestor));
    }
}
