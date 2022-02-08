<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V104 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Managing admin settings
        //

        // New settings
        \DB::table('settings')->insert(array (
            array (
                'key' => 'media.enforce_mp4_conversion',
                'display_name' => 'Enforce mp4 videos re-conversion',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description": "Allows you skip mp4 re-conversion to platform standards, saving upload time and CPU usage. Recommended value: On."
}',
                'type' => 'checkbox',
                'order' => 13,
                'group' => 'Media',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'media.max_videos_length',
                'display_name' => 'Max videos length',
                'value' => '0',
                'details' => '{
"description": "Maximum videos length, specified in seconds. (0 = unlimited)"
}',
                'type' => 'text',
                'order' => 15,
                'group' => 'Media',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'site.custom_css',
                'display_name' => 'Custom CSS Code',
                'value' => '',
                'type' => 'code_editor',
                'order' => 90,
                'group' => 'Site',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'site.custom_js',
                'display_name' => 'Custom JS Code',
                'value' => '',
                'type' => 'code_editor',
                'order' => 100,
                'group' => 'Site',
            )));


        \DB::table('settings')->insert(array (
            array (
                'key' => 'feed.feed_suggestions_autoplay',
                'display_name' => 'Autoplay suggestions box slides',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
}',
                'value' => '0',
                'type' => 'checkbox',
                'order' => 110,
                'group' => 'Feed',
            )));

        // Settings updates
        DB::table('settings')
            ->where('key', 'media.max_file_upload_size')
            ->update(['details' => '{
"description":  "File size in MB. Do not exceed PHP maximum upload size & post size as video uploads might silently fail."
}'
            ]);

        DB::table('settings')
            ->where('key', 'site.currency_code')
            ->update([
                'group'=>'Payments',
                'key' => 'payments.currency_code'
            ]);

        DB::table('settings')
            ->where('key', 'site.currency_symbol')
            ->update([
                'group'=>'Payments',
                'key' => 'payments.currency_symbol'
            ]);

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
