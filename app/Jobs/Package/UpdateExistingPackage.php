<?php

namespace App\Jobs\Package;

use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;

class UpdateExistingPackage extends BaseJob
{
    /**
     * @var \App\Entities\Question\Package
     */
    public $package;

    /**
     * UpdateExistingPackage constructor.
     *
     * @param \App\Entities\Question\Package $package
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(Package $package, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->package = $package;

        $this->inputs = $this->validate($this->request, [
            'title' => 'required',
            'level' => 'nullable|numeric',
            'description' => 'nullable',
        ]);
    }

    /**
     * Run the actual command process.
     *
     * @return bool
     */
    public function run(): bool
    {
        $this->package->fill($this->inputs);

        return $this->package->save();
    }
}
