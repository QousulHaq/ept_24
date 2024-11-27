<?php

namespace App\Extra\Repositories\Item;

use App\Entities\Question\Package;
use App\Entities\Question\Package\Item;

class TypeMultiChoiceSingleRepository extends BaseTypeRepository
{
    /**
     * @var \App\Entities\Question\Package\Item
     */
    public Item $item;

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

    public function __construct(Package $package, Item $item, array $config, int $index)
    {
        $this->item = $item;
        $this->index = $index;
        $this->config = $config;
        $this->package = $package;
    }
}
