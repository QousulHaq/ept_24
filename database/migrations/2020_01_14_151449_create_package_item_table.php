<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_item', function (Blueprint $table) {
            $table->uuid('package_id');
            $table->uuid('item_id');
            $table->smallInteger('score')->default(0);
            $table->smallInteger('order')->default(0);

            $table->primary(['package_id', 'item_id']);

            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
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
        Schema::dropIfExists('package_item');
    }
}
