<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('parent_id')->index()->nullable();

            // simple, multi_choice_single, multi_choice, fill_in_blank, essay, true_false, bundle.
            $table->string('type')->default('simple');
            $table->string('code')->nullable();
            $table->text('content')->nullable();
            $table->boolean('answer_order_random')->default(1);
            $table->smallInteger('duration')->default(0);
            $table->integer('item_count')->default(1);
            $table->smallInteger('order')->default(0);
            $table->timestamps();

            $table->primary('id');

            $table->foreign('parent_id')
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
        Schema::dropIfExists('items');
    }
}
