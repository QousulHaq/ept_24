<?php

use App\Entities\Question\Package;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeInPackageItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_item', function (Blueprint $table) {
            $table->string('type', 8)->default(Package::PACKAGE_ITEM_TYPE_QUESTION);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_item', function (Blueprint $table) {
            $table->dropColumn('type', 8);
        });
    }
}
