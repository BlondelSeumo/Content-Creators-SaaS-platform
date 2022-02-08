<?php

use Database\Seeds\PublicPagesTableSeeder;
use Illuminate\Database\Seeder;

use Database\Seeds\DataTypesTableSeeder;
use Database\Seeds\DataRowsTableSeeder;
use Database\Seeds\MenusTableSeeder;
use Database\Seeds\MenuItemsTableSeeder;
use Database\Seeds\RolesTableSeeder;
use Database\Seeds\PermissionsTableSeeder;
use Database\Seeds\PermissionRoleTableSeeder;
use Database\Seeds\UserRolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // $this->call(UsersTableSeeder::class);
        $this->call(DataTypesTableSeeder::class);
        $this->call(DataRowsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(UserRolesTableSeeder::class);
        $this->call(InsertCountries::class);
        $this->call(PublicPagesTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
