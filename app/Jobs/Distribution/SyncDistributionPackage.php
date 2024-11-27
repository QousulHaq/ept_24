<?php

namespace App\Jobs\Distribution;

use App\Entities\Classification;
use App\Entities\Question\Package;
use App\Extra\Distribution;
use App\Extra\Eloquent\Scopes\RootEntityScope;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class SyncDistributionPackage implements ShouldQueue, Responsable
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $attributes;

    /**
     * Create a new job instance.
     *
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(array $inputs)
    {
        $this->attributes = Validator::make($inputs, [
            'base_uri' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
            'package_id' => 'required',
        ])->validate();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function handle(): void
    {
        $connector = $this->getConnector();
        $service = $connector->getService();
        $key = $service->getCompositeKeys($this->attributes['package_id']);
        $packageFromRemote = $service->getPackageDetail($this->attributes['package_id']);

        $this->packageCreator($key, $packageFromRemote);
    }

    private function packageCreator(array $key, array $packageFromRemote): Package
    {
        /** @var Package $packageModel */
        $packageModel = Package::query()
            ->withoutGlobalScope(RootEntityScope::class)
            ->where('id', $packageFromRemote['id'])
            ->firstOrNew();

        $packageModel->forceFill(array_merge(Arr::except($packageFromRemote, ['children', 'classifications']), [
            'is_encrypted' => true,
            'distribution_options' => array_merge($this->attributes, [
                'public_key' => $key['public_key'],
                'has_passphrase' => $key['has_passphrase'],
                'secret' => $key['secret'],
            ]),
        ]));

        $packageModel->saveQuietly();

        if ($packageModel->exists && array_key_exists('classifications', $packageFromRemote) && count($packageFromRemote['classifications']) > 0) {
            collect($packageFromRemote['classifications'])->each($this->classificationImporter($packageModel));
        }

        $batch = $this->batch();

        if ($batch && $packageModel->exists) {
            $batch->add([
                new SyncItemFromOrigin($packageModel),
            ]);
        } elseif ($packageModel->exists) {
            dispatch_sync(new SyncItemFromOrigin($packageModel));
        }

        if ($packageFromRemote['children'] && count($packageFromRemote['children']) > 0) {
            foreach ($packageFromRemote['children'] as $package) {
                $this->packageCreator($key, $package);
            }
        }

        return $packageModel;
    }

    private function getConnector(): Distribution\Connector
    {
        return $this->getDistribution()->getConnector(Arr::except($this->attributes, ['package_id']));
    }

    private function getDistribution(): Distribution
    {
        return app(Distribution::class);
    }

    private function classificationImporter(Package $package): \Closure
    {
        return static function (array $classification) use ($package) {
            /** @var Classification $classificationModel */
            $classificationModel = Classification::query()->firstOrCreate(Arr::only($classification, ['type', 'name']), $classification);

            $package->classifications()->syncWithoutDetaching([$classificationModel->id]);
        };
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'package' => Package::query()->find($this->attributes['package_id']),
        ]);
    }
}
