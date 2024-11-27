<?php

namespace App\Extra\Contracts;

use App\Entities\CBT\Participant;
use App\Extra\CBT;
use Illuminate\Contracts\Support\Arrayable;

interface Preset extends Arrayable
{
    public function getName(): string;

    public function getCode(): string;

    public function getInfo(): string;

    public function registerConfig(CBT $cbt): void;

    public function getScore(Participant $participant): float;
}
