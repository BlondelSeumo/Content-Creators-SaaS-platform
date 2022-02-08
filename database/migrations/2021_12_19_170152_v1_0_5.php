<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V105 extends Migration
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
                'key' => 'storage.was_access_key',
                'display_name' => 'Wasabi Access Key',
                'value' => '',
                'type' => 'text',
                'order' => 111,
                'group' => 'Storage',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'storage.was_secret_key',
                'display_name' => 'Wasabi Secret Key',
                'value' => '',
                'type' => 'text',
                'order' => 112,
                'group' => 'Storage',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'storage.was_region',
                'display_name' => 'Wasabi Region',
                'value' => '',
                'type' => 'text',
                'order' => 113,
                'group' => 'Storage',
            )));

        \DB::table('settings')->insert(array (
            array (
                'key' => 'storage.was_bucket_name',
                'display_name' => 'Wasabi Bucket',
                'value' => '',
                'type' => 'text',
                'order' => 114,
                'group' => 'Storage',
            )));

        // Settings updates
        DB::table('settings')
            ->where('key', 'storage.driver')
            ->update(['details' => '{
"default" : "s3",
"options" : {
"public": "Local",
"s3": "S3",
"wasabi": "Wasabi"
}
}'
            ]);

        DB::table('settings')
            ->where('key', 'storage.aws_cdn_key_pair_id')
            ->update(['display_name' => 'Aws CloudFront Key Pair Id'
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
