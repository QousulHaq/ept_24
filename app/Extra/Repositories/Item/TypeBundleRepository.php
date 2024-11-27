<?php

namespace App\Extra\Repositories\Item;

use App\Entities\Question\Package;
use Illuminate\Support\Collection;
use App\Entities\Question\Package\Item;

class TypeBundleRepository extends BaseTypeRepository
{
    /**
     * @var \App\Entities\Question\Package\Item
     */
    public Item $parent;

    /**
     * @var \Illuminate\Support\Collection
     */
    public Collection $children;

    /**
     * @var int
     */
    public int $index;

    /**
     * @var array
     */
    public array $config;

    /**
     * @var \App\Entities\Question\Package
     */
    public Package $package;

    public function __construct(Package $package, Item $parent, Collection $children, array $config, int $index)
    {
        $this->parent = $parent;
        $this->children = $children;
        $this->index = $index;
        $this->config = $config;
        $this->package = $package;
    }
}
