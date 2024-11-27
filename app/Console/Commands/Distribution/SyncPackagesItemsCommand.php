<?php

namespace App\Console\Commands\Distribution;

use App\Entities\Question\Package;
use App\Jobs\Distribution\SyncDistributionPackage;
use Illuminate\Bus\PendingBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SyncPackagesItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribution:sync {package=all} {--queue=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync package from origin.';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Throwable
     */
    public function handle(): int
    {
        $packageId = $this->argument('package');

        $query = Package::query()->where('is_encrypted', true);

        if ($packageId !== 'all') {
            $query->where('id', $packageId);
        }

        if (! (bool) $this->option('queue')) {
            $query->get()
                ->each(fn(Package $package) => $this->info('package : '.$package->title.' will be sync.'))
                ->each(fn(Package $package) => dispatch_sync(new SyncDistributionPackage($package->distribution_options)));
        } else {
            $query->get()
                ->each(fn(Package $package) => $this->info('package : '.$package->title.' will be sync in queue.'))
                ->map(fn(Package $package) => Bus::batch([new SyncDistributionPackage($package->distribution_options)])->name('sync distribution package'))
                ->each(fn(PendingBatch $bus) => $bus->dispatch());
        }

        return 0;
    }
}
