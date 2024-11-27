<?php

namespace App\Extra\Eloquent\Concerns;

use Mockery\Exception;
use App\Extra\Contracts\Preset;

/**
 * Trait HasConfigCBT.
 *
 * @property-read Preset $preset
 * @property-read array $config
 */
trait HasConfigCBT
{
    /**
     * @return \App\Extra\Contracts\Preset|null
     * @throws \Throwable
     */
    public function getPresetAttribute(): Preset
    {
        return cbt()->getPreset(explode('.', $this->getRawOriginal('config'))[0]);
    }

    /**
     * @return array|null
     * @throws \Throwable
     */
    public function getConfigAttribute(): ?array
    {
        $originalConfig = $this->getRawOriginal('config');

        // check if this new object...
        if (! $this->getKey()) return null;

        throw_if($originalConfig === null, Exception::class,
            'config is null on '.get_class($this).'@'.$this->getKey());

        return cbt()->getConfig($originalConfig);
    }
}
