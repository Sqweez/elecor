<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('amount')->default(0);
            $table->integer('bonuses')->default(0);
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('bonus_transaction_id')->default(null);
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('online_payments');
    }
}
