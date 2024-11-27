<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_package', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained('oauth_clients')->cascadeOnDelete();
            $table->foreignUuid('package_id')->constrained('packages')->cascadeOnDelete();
            $table->longText('private_key');
            $table->longText('public_key');
            $table->string('passphrase')->nullable();
            $table->text('secret');
            $table->timestamp('last_sync')->nullable();
            $table->primary(['client_id', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_package');
    }
}
