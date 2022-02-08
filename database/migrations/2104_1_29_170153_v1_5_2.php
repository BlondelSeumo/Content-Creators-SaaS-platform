<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V152 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('settings')
            ->where('id', 1)
            ->update([
                'key' => 'site.name',
                'display_name' => 'Site name',
            ]);

        DB::table('settings')
            ->where('key', 'site.description')
            ->update([
                'display_name' => 'Site description',
            ]);

        DB::table('settings')
            ->where('key', 'site.app_url')
            ->update(['order' => 0]);

        DB::table('settings')->insert(array (
            array (
                'key' => 'site.slogan',
                'display_name' => 'Site slogan',
                'value' => '',
                'type' => 'text',
                'order' => 4,
                'group' => 'Site',
            )));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
