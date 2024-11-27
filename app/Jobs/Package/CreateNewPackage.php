<?php

namespace App\Jobs\Package;

use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Extra\Contracts\Preset;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;
use Illuminate\Contracts\Support\Responsable;
use App\Events\Package\PackageCreatedOrUpdated;

class CreateNewPackage extends BaseJob implements Responsable
{
    public Package $package;

    public Preset $preset;

    /**
     * CreateNewPackage constructor.
     *
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function __construct(array $inputs = [])
    {
        parent::__construct($inputs);

        $this->package = new Package();
        $this->preset = cbt()->getPreset($this->request->input('preset'));

        $this->inputs = $this->validate($this->request, [
            'title' => 'required',
            'level' => 'nullable|numeric',
            'description' => 'nullable',
        ]);

        $this->inputs['code'] = $this->generateCode();
        $this->inputs['config'] = $this->preset->getCode();

        self::onSuccess(fn () => event(new PackageCreatedOrUpdated($this->package->fresh())));
    }

    public function generateCode(): string
    {
        return $this->preset->getCode().'#'.Str::random(4);
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

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'data' => $this->package,
        ]);
    }
}
