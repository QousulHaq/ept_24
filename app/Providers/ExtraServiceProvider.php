<?php

namespace App\Providers;

use App\Extra\CBT;
use App\Extra\Distribution;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Configuration;

class ExtraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCBTInstance();
        $this->registerDistributionInstance();
        $this->registerJWTInstance();
    }

    private function registerCBTInstance()
    {
        $this->app->singleton(CBT::class, fn() => new CBT(config('cbt.presets')));

        $this->app->alias(CBT::class, 'cbt');
    }

    private function registerJWTInstance()
    {
        $this->app->singleton('cbt.jwt.token.config', fn() => Configuration::forUnsecuredSigner());
    }

    private function registerDistributionInstance()
    {
        $this->app->singleton(Distribution::class, fn() => new Distribution());
    }
}
