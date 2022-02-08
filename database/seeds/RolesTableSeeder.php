<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'Administrator',
                'created_at' => '2021-08-07 18:52:09',
                'updated_at' => '2021-08-07 18:52:09',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'user',
                'display_name' => 'Normal User',
                'created_at' => '2021-08-07 18:52:09',
                'updated_at' => '2021-08-07 18:52:09',
            ),
        ));
        
        
    }
}