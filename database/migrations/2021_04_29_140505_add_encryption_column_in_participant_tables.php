<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptionColumnInParticipantTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participant_section_items', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_id')->nullable();
        });

        Schema::table('participant_section_item_answers', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant_section_items', function (Blueprint $table) {
            $table->dropColumn('is_encrypted');
            $table->dropColumn('encryption_id');
        });

        Schema::table('participant_section_item_answers', function (Blueprint $table) {
            $table->dropColumn('is_encrypted');
            $table->dropColumn('encryption_id');
        });
    }
}
