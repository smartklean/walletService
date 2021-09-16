<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypeInBusinessConsumerssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_consumers', function (Blueprint $table) {
            $table->boolean('is_live')->default(false)->change();
            $table->boolean('is_blacklisted')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_consumers', function (Blueprint $table) {
            $table->integer('is_live')->default(0)->change();
            $table->integer('is_blacklisted')->default(0)->change();
        });
    }
}
