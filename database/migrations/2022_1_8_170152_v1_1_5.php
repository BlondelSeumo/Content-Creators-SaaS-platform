<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V115 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Settings updates
        \DB::table('settings')->insert(array (
            array (
                'key' => 'site.hide_identity_checks',
                'display_name' => 'Hide identity checks menu',
                'details' => '{
"on" : "On"
"off" : "Off",
"checked" : false,
\'description\' : \'If enabled, users will only be able to post content if verified.\'
}',
                'value' => '0',
                'type' => 'checkbox',
                'order' => 77,
                'group' => 'Site',
            )));


        \DB::table('settings')->insert(array (
            array (
                'key' => 'feed.suggestions_skip_empty_profiles',
                'display_name' => 'Skip empty profiles out of the suggestions list',
                'details' => '{
"on" : "On"
"off" : "Off",
"checked" : false,
\'description\' : \'If enabled, users will only get suggestions of profiles that have both avatar and cover filled in.\'
}',
                'value' => '0',
                'type' => 'checkbox',
                'order' => 72,
                'group' => 'Feed',
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
