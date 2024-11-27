<?php

namespace App\Http\Resources\Client\Sync;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class PackageResource
 * @package App\Http\Resources\Client\Sync
 *
 * @property-read  \App\Entities\Question\Package $resource
 */
class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = $this->resource->getRawOriginal();

        $data = array_filter($data, static fn($key) => ! Str::contains($key, 'pivot'), ARRAY_FILTER_USE_KEY);

        try {
            $data['note'] = json_decode($data['note'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $data['note'] = null;
        }

        $data['classifications'] = $this->resource->classifications;

        $data['children'] = $this->resource->children->count() > 0
            ? PackageResource::collection($this->resource->children)
            : [];

        return $data;
    }
}
