<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V110 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // User table updates
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('paid_profile')->default(1)->after('public_profile');
            });
        }

        //New admin settings
        \DB::table('settings')->insert(array (
            array (
                'key' => 'feed.allow_gallery_zoom',
                'display_name' => 'Allow post galleries Zoom in',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description": "If enabled, high res photos will feature a zoom in/out option."
}',
                'value' => '0',
                'type' => 'checkbox',
                'order' => 115,
                'group' => 'Feed',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'media.use_url_watermark',
                'display_name' => 'Use profile url watermark',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description": "If enabled, the media will include a watermark containing the user profile URL."
}',
                'value' => '0',
                'type' => 'checkbox',
                'order' => 116,
                'group' => 'Media',
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
