<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_sections', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('config')->nullable();
            $table->uuid('participant_id');
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('item_duration')->default(false);
            $table->integer('remaining_time')->default(0);
            $table->smallInteger('attempts')->default(0);
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->primary('id');

            $table->foreign('participant_id')
                ->references('id')
                ->on('participants')
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
        Schema::dropIfExists('participant_sections');
    }
}
