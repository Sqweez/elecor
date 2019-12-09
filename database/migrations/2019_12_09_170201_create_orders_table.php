<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('client_comment', 1000)->nullable();
            $table->string('service_id');
            $table->integer('user_id')->nullable();
            $table->string('comment', 200)->nullable();
            $table->boolean('is_worked')->default(false);
            $table->text('push_token')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
