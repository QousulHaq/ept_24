<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForeignInParticipantSectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('participant_section_items', function (Blueprint $table) {
                $table->dropForeign('participant_section_items_item_id_foreign');
                $table->foreign('item_id')
                    ->references('id')
                    ->on('items')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('participant_section_items', function (Blueprint $table) {
                $table->dropForeign('participant_section_items_item_id_foreign');
                $table->foreign('item_id')
                    ->references('id')
                    ->on('items')
                    ->onDelete('cascade');
            });
        }
    }
}
