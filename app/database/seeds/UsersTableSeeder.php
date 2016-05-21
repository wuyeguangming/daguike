<?php

class UsersTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();


        $users = array(
            array(
                'username'      => 'admin',
                'email'      => 'admin@daguike.com',
                'password'   => Hash::make('dgk880525'),
                'confirmed'   => 1,
                'confirmation_code' => md5(microtime().Config::get('app.key')),
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
                'loc_province' => 1,
                'loc_city' => 2,
                'loc_district' => 3,
                'loc_community' => 4,
                // 'store_id'  => 1
            ),
            array(
                'username'      => 'user',
                'email'      => 'user@daguike.com',
                'password'   => Hash::make('dgk880525'),
                'confirmed'   => 1,
                'confirmation_code' => md5(microtime().Config::get('app.key')),
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
                'loc_province' => 1,
                'loc_city' => 2,
                'loc_district' => 3,
                'loc_community' => 4,
                // 'store_id'  => ''
            )
        );

        DB::table('users')->insert( $users );
    }

}
