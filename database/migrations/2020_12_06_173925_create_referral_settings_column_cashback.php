<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralSettingsColumnCashback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referral_settings', function (Blueprint $table) {
            $table->integer('cashback')->default(1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referral_settings', function (Blueprint $table) {
            $table->dropColumn('cashback');
        });
    }
}
