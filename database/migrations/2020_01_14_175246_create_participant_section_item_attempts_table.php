<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantSectionItemAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_section_item_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('participant_section_item_id');
            $table->smallInteger('attempt_number')->default(1);
            $table->text('answer')->nullable();
            $table->integer('score')->default(0);
            $table->boolean('is_correct')->default(0);
            $table->timestamps();

            $table->foreign('participant_section_item_id', 'item_attempt_fk')
                ->references('id')
                ->on('participant_section_items')
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
        Schema::dropIfExists('participant_section_item_attempts');
    }
}
