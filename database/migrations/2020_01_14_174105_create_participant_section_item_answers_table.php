<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantSectionItemAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_section_item_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('participant_section_item_id');
            $table->text('content')->nullable();
            $table->boolean('correct_answer')->default(0);
            $table->smallInteger('order');
            $table->timestamps();

            $table->foreign('participant_section_item_id', 'item_answer_fk')
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
        Schema::dropIfExists('participant_section_item_answers');
    }
}
