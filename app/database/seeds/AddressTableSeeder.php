<?php

class AddressTableSeeder extends Seeder {

    public function run()
    {
        DB::table('addresses')->delete();


        // $address = array(
        //     'name' => '11',
        //     'phone' => '11111111111',
        //     'loc_room' =>  '9',
        //     'user_id' =>  '3'
        // );

        // DB::table('addresses')->insert( $address );
    }

}
