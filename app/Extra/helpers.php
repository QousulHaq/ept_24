<?php

if (! function_exists('cbt')) {
    function cbt(): App\Extra\CBT
    {
        return app(App\Extra\CBT::class);
    }
}

if (! function_exists('force_queue_sync')) {
    function force_queue_sync(Closure $callback)
    {
        $target = 'queue.default';

        $original = config($target);
        config()->set($target, 'sync');
        $callback();
        config()->set($target, $original);
    }
}
