<?php

namespace App\Jobs\Package;

use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;

class DeleteExistingPackage extends BaseJob
{
    /**
     * @var \App\Entities\Question\Package
     */
    public $package;

    /**
     * @var bool
     */
    private $force;

    /**
     * DeleteExistingPackage constructor.
     *
     * @param \App\Entities\Question\Package $package
     * @param bool $force
     * @param array $inputs
     */
    public function __construct(Package $package, bool $force = false, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->package = $package;
        $this->force = $force;
    }

    /**
     * Run the actual command process.
     *
     * @return bool
     * @throws \Exception
     */
    public function run(): bool
    {
        return $this->force ? $this->package->forceDelete() : $this->package->delete();
    }
}
