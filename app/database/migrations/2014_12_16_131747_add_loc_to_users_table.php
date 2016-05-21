<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocToUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        // add loc_* to the users table
        Schema::table('users', function($table){
            $table->integer('loc_province')->unsigned();
            $table->integer('loc_city')->unsigned();
            $table->integer('loc_district')->unsigned();
            $table->integer('loc_community')->unsigned();
            $table->integer('loc_building')->unsigned()->nullable();
            $table->integer('loc_room')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('users', function($table){
            $table->dropColumn(array('loc_province', 'loc_city', 'loc_district', 'loc_community'));
        });
    }

}
