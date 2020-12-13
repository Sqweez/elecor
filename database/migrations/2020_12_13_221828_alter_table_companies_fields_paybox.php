<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompaniesFieldsPaybox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('paybox_id')->nullable();
            $table->string('paybox_secret_word')->nullable();
            $table->boolean('can_pay')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('paybox_id');
            $table->dropColumn('paybox_secret_word');
            $table->dropColumn('can_pay');
        });
    }
}
