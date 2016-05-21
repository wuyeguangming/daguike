<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
        Schema::create('addresses', function($table){
            $table->increments('id');
            $table->string('name',50);
            $table->string('phone',11);
            $table->boolean('confirmed')->default(false);
            $table->string('confirm_code',10)->nullable();
            $table->integer('loc_room')->unsigned();
            // $table->dateTime('time'); 
            $table->string('note',100)->default('')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // $table->integer('wxuser_id')->unsigned();
            // $table->foreign('wxuser_id')->references('id')->on('wxusers')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('addresses');
	}

}
