<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;
class CreateReferralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message_template')->nullable();
            $table->string('base_url')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('hash_ref')->default(true);
        });

        DB::table('referral_settings')->insert([
            'message_template' => 'Шаблонное сообщение! Ссылка для установки:',
            'base_url' => 'https://elecor.kz/install',
            'discount' => 2000,
            'hash_ref' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_settings');
    }
}
