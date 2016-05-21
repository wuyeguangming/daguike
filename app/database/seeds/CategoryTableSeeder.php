<?php

class CategoryTableSeeder extends Seeder {

    public function run()
    {
        DB::table('categories')->delete();
        $categories = array(
            array('id' => '1','name' => '水果','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '0','sort' => '1','is_show' => '1','title' => '','keywords' => '','description' => ''),
            array('id' => '2','name' => '国产','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '1','sort' => '1','is_show' => '1','title' => '','keywords' => '','description' => ''),
            array('id' => '3','name' => '苹果','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '2','sort' => '2','is_show' => '1','title' => '','keywords' => '','description' => ''),
            array('id' => '4','name' => '进口','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '1','sort' => '2','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '5','name' => '车厘子','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '4','sort' => '1','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '6','name' => '服装','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '0','sort' => '2','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '7','name' => '男装','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '6','sort' => '1','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '8','name' => 'T恤','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '7','sort' => '2','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '9','name' => '女装','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '6','sort' => '2','is_show' => '1','title' => '','keywords' => '','description' => ''),
            // array('id' => '10','name' => '裙子','pic' => NULL,'type_id' => '0','type_name' => '','store_id' => '1','parent_id' => '9','sort' => '1','is_show' => '1','title' => '','keywords' => '','description' => ''),
        );
        DB::table('categories')->insert( $categories );
    }

}
