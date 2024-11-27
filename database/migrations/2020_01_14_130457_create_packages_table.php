<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('parent_id')->nullable();
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('config')->nullable();
            $table->integer('depth')->default(0);
            $table->string('description')->nullable();
            $table->integer('level')->nullable();
            $table->smallInteger('duration')->default(0);
            $table->smallInteger('max_score')->default(0);
            $table->boolean('random_item')->default(0);
            $table->boolean('item_duration')->default(0);
            $table->timestamps();

            $table->primary('id');

            $table->foreign('parent_id')
                ->references('id')
                ->on('packages')
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
        Schema::dropIfExists('packages');
    }
}
