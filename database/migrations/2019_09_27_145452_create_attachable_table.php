<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachable', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('attachment_id')->index();
            $table->string('attachable_type');
            $table->uuid('attachable_uuid')->nullable();
            $table->unsignedInteger('attachable_id')->nullable();
            $table->integer('order')->default(1);

            $table->index(['attachable_uuid', 'attachable_type'], 'attachable_uuid_index');
            $table->index(['attachable_id', 'attachable_type'], 'attachable_id_index');

            $table->foreign('attachment_id')
                ->references('id')
                ->on('attachments')
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
        Schema::dropIfExists('attachable');
    }
}
