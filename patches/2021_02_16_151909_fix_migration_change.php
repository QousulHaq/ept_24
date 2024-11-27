<?php

use Jalameta\Patcher\Patch;

class FixMigrationChange extends Patch
{
    /**
     * Run patch script.
     *
     * @return void
     */
    public function patch()
    {
        \Illuminate\Support\Facades\DB::table('migrations')
            ->where('migration', '2020_02_26_070115_create_websockets_statistics_entries_table')
            ->update([
                'migration' => '0000_00_00_000000_create_websockets_statistics_entries_table',
            ]);
    }
}
