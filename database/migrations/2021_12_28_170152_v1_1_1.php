<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V111 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('settings')
            ->where('key', 'media.allowed_file_extensions')
            ->update([
                'value'=>'png,jpg,jpeg,wav,mp3,ogg,mp4,avi,mov,moov,m4v,mpeg,wmv,avi,asf',
            ]);

        \DB::table('settings')->insert(array (
            array (
                'key' => 'ad-spaces.sidebar_ad_spot',
                'display_name' => 'Sidebar Ad',
                'details' => '{
    "description" : "Will be shown on user feed & profiles, over the right sidebar."
}',
                'value' => '',
                'type' => 'code_editor',
                'order' => 117,
                'group' => 'Ad spaces',
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
