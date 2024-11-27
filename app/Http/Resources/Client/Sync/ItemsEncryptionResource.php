<?php

namespace App\Http\Resources\Client\Sync;

use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use App\Extra\Distribution;
use App\Extra\Repositories\ClientRepository;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class ItemsEncryptionResource extends ResourceCollection
{
    private ?Distribution\Encryptor $encryptor;

    public function __construct($resource, ?Package $rootPackage = null, ?Distribution\Encryptor $encryptor = null)
    {
        parent::__construct($resource);

        if (! $encryptor && $rootPackage) {
            $secret = self::getClientRepository()->getDistributedPropertyFor($rootPackage, 'secret');
            $this->encryptor = self::getDistribution()->getEncryptor($secret);
        } else if ($encryptor) {
            $this->encryptor = $encryptor;
        } else {
            throw new \LogicException('must fill params #2 $rootPackage or #3 $encryptor.');
        }
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (Package\Item $item) {
            $data = $item->getRawOriginal();

            [$data, $pivot] = $this->pivotRemover($data);

            if ($item->children->count() > 0) {
                $data['children'] = new static($item->children, encryptor: $this->encryptor);
            }

            $data['content'] = $this->encryptor->encrypt($data['content']);

            $data['pivot'] = $pivot;

            $data['classifications'] = $item->classifications->toArray();

            $data['answers'] = $item->answers->map(function(Package\Item\Answer $answer) {
                $data = $answer->getRawOriginal();

                $data['content'] = $this->encryptor->encrypt($data['content']);

                return $data;
            });

            $data['attachments'] = $item->attachments()->withPivot('id')->get()->map(function (Attachment $attachment) {
                $data = $attachment->getRawOriginal();

                [$data, $pivot] = $this->pivotRemover($data);

                try {
                    $data['options'] = json_decode($data['options'], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    $data['options'] = null;
                }

                $data['pivot'] = $pivot;

                return $data;
            });

            return $data;
        })->toArray();
    }

    private function pivotRemover(array $data): array
    {
        $pivot = [];

        $data = collect($data)
            ->filter(function ($value, $key) use (&$pivot) {
                $isPivot = Str::startsWith($key, 'pivot');

                if ($isPivot) {
                    $pivot[str_replace('pivot_', '', $key)] = $value;
                }

                return ! $isPivot;
            })
            ->toArray();

        return [$data, $pivot];
    }

    private static function getClientRepository(): ClientRepository
    {
        return app(ClientRepository::class);
    }

    private static function getDistribution(): Distribution
    {
        return app(Distribution::class);
    }
}
