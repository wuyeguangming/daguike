<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
        // Creates the locations table
        Schema::create('locations', function($table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name',50);
            $table->integer('sid')->unsigned()->unique();
            $table->integer('parent')->unsigned();
            $table->tinyInteger('level')->unsigned();
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::drop('locations');
	}

}
