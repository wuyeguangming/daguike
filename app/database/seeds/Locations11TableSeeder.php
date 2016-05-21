<?php

class Locations11TableSeeder extends Seeder {

    public function run()
    {
        $pid = DB::table('locations')->count();
        $locations = array();
        $locations[] = array(
            'name'       => '11幢',
            'sid'        => $pid,
            'parent'     => 4,
            'level'      => 5,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        );

        for ($ii=2; $ii < 21; $ii++) { 
            for ($iii=1; $iii <= 35; $iii++) { 
                $name = sprintf("%02d", $ii).sprintf("%02d", $iii);
                $locations[] = array(
                    'name'       => $name,
                    'sid'        => $pid+count($locations)+1,
                    'parent'     => $pid,
                    'level'      => 6,
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime,
                );
            }
        }

        DB::table('locations')->insert( $locations );
    }

}
