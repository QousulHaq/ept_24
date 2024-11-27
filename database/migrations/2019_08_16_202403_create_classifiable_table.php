<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassifiableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifiable', function (Blueprint $table) {
            $table->unsignedBigInteger('classification_id');
            $table->uuidMorphs('classifiable', 'classifiable_morph_index');

            $table->primary(['classification_id', 'classifiable_id', 'classifiable_type'], 'classifiable_primary');

            $table->foreign('classification_id')
                ->references('id')
                ->on('classifications')
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
        Schema::dropIfExists('classifiable');
    }
}
