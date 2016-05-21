<?php

class LocationsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('locations')->delete();

        $locations = array(
            array(
                'name'       => '中国',
                'sid'        => 0,
                'parent'     => 0,
                'level'      => 0,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
            array(
                'name'       => '浙江',
                'sid'        => 1,
                'parent'     => 0,
                'level'      => 1,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
            array(
                'name'       => '杭州',
                'sid'        => 2,
                'parent'     => 1,
                'level'      => 2,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
            array(
                'name'       => '下沙',
                'sid'        => 3,
                'parent'     => 2,
                'level'      => 3,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
            array(
                'name'       => '中沙金座',
                'sid'        => 4,
                'parent'     => 3,
                'level'      => 4,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
        );
        for ($i=1; $i < 11; $i++) {
            $pid = count($locations);
            $locations[] = array(
                'name'       => $i.'幢',
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
                        'sid'        => count($locations),
                        'parent'     => $pid,
                        'level'      => 6,
                        'created_at' => new DateTime,
                        'updated_at' => new DateTime,
                    );
                }
            }
        }


        DB::table('locations')->insert( $locations );
    }

}
