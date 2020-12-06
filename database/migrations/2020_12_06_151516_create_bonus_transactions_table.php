<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->integer('amount');
            $table->integer('connection_id')->default(-1);
            $table->integer('operation_type');
            $table->text('comment')->nullable();
            $table->integer('user_id')->default(-1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_transactions');
    }
}
