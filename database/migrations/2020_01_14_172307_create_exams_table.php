<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('package_id')->nullable();
            $table->string('name');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->smallInteger('duration')->default(0);
            $table->boolean('is_anytime')->default(0);
            $table->boolean('is_multi_attempt')->default(0);
            $table->boolean('automatic_start')->default(0);
            $table->timestamps();

            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
