<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocDetailToAddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('addresses', function($table){
            $table->string('loc_detail',100)->default('')->nullable();
            $table->integer('loc_building')->unsigned();
        });
        $addresses = Address::all();
        foreach ($addresses as $index => $address) {
        	$loc_room = Location::where('sid','=',$address->loc_room)->first();
        	$loc_building = Location::where('sid','=',$loc_room->parent)->first();
        	$address->loc_detail = $loc_room->name;
        	$address->loc_building = $loc_building->sid;
        	$address->save();
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('addresses', function($table){
            $table->dropColumn(array('loc_detail'));
            $table->dropColumn(array('loc_building'));
        });
	}

}
