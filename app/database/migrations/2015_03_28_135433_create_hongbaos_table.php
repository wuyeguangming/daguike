<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHongbaosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('hongbaos', function($table){
            $table->bigIncrements('id');
            $table->string('name',50);
            $table->boolean('used')->default(0);
            $table->dateTime('time_start'); 
            $table->dateTime('time_end');
            $table->decimal('amount',10,2); //金额
            $table->decimal('condition',10,2); 	//使用条件：满XX

            $table->timestamps();
            $table->softDeletes();
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hongbaos');
	}

}
