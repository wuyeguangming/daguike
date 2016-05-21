<?php

class DatabaseSeeder extends Seeder {

    public function run(){
        Eloquent::unguard();

        $this->call('UsersTableSeeder');
        $this->call('PostsTableSeeder');
        $this->call('CommentsTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('PermissionsTableSeeder');
        $this->call('StoresTableSeeder');
        $this->call('StoreClassTableSeeder');
        $this->call('AlbumTableSeeder');
        $this->call('LocationsTableSeeder');
        $this->call('CategoryTableSeeder');
        $this->call('GoodsTableSeeder');
        $this->call('SkuTableSeeder');
        $this->call('AddressTableSeeder');
        $this->call('Locations11TableSeeder'); //php artisan db:seed --class=Locations11TableSeeder

    }

}