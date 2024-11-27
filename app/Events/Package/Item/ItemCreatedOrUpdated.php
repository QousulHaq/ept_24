<?php

namespace App\Events\Package\Item;

use App\Entities\Question\Package;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ItemCreatedOrUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var \App\Entities\Question\Package\Item
     */
    protected Package\Item $item;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\Question\Package\Item $item
     */
    public function __construct(Package\Item $item)
    {
        $this->item = $item;
    }
}
