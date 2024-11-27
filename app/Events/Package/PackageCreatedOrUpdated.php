<?php

namespace App\Events\Package;

use App\Entities\Question\Package;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class PackageCreatedOrUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * @var \App\Entities\Question\Package
     */
    public Package $package;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\Question\Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }
}
