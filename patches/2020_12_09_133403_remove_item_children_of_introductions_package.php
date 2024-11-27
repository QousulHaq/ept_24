<?php

use App\Entities\Question\Package;
use Illuminate\Support\Facades\DB;
use Jalameta\Patcher\Patch;

class RemoveItemChildrenOfIntroductionsPackage extends Patch
{

    /**
     * Run patch script.
     *
     * @return void
     */
    public function patch()
    {
        DB::transaction(function () {
            Package::all()->each(fn(Package $package) => $this->scanPackages($package));
        });
    }

    /**
     * @param \App\Entities\Question\Package $package
     * @throws \Exception
     */
    public function scanPackages(Package $package)
    {
        $package->introductions->each(fn(Package\Item $item) => $this->getIntroItemChildren($item, $package));

        $package->children->each(fn(Package $package) => $this->scanPackages($package));
    }

    /**
     * @param \App\Entities\Question\Package\Item $item
     * @param \App\Entities\Question\Package $package
     * @throws \Exception
     */
    public function getIntroItemChildren(Package\Item $item, Package $package)
    {
        $item->children->each(function (Package\Item $item) use ($package) {
             $this->command->info('deleting sub item '.$item->code.':'.$item->id.' from package '.$package->title);
             $item->delete();
        });
    }
}
