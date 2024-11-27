<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantSectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_section_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('section_id');
            $table->uuid('item_id')->nullable();
            $table->string('config')->nullable();
            $table->string('type')->default('simple');
            $table->string('label');
            $table->text('content');
            $table->text('sub_content')->nullable();
            $table->smallInteger('remaining_time')->default(0);
            $table->smallInteger('order')->default(0);
            $table->timestamps();

            $table->foreign('section_id')
                ->references('id')
                ->on('participant_sections')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant_section_items');
    }
}
