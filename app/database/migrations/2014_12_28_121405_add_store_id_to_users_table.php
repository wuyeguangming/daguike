<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreIdToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
        Schema::table('users', function($table){
            $table->integer('store_id')->unsigned()->nullable()->index();
            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
        Schema::table('users', function($table){
            $table->dropForeign('users_store_id_foreign');
            // $table->dropColumn(array('store_id'));
        });
	}

}
