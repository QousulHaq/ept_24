<?php

use Database\Seeders\AttachmentsTableSeeder;
use Jalameta\Patcher\Patch;

class AddAttachmentBeep extends Patch
{
    /**
     * Run patch script.
     *
     * @return void
     * @throws \Exception
     */
    public function patch()
    {
        $seeder = new AttachmentsTableSeeder();

        $seeder->setCommand($this->command);

        $seeder->run();
    }
}
