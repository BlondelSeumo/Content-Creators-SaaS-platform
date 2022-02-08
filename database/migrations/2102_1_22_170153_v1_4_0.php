<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V140 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('auth_provider')->nullable()->after('remember_token');
                $table->string('auth_provider_id')->after('auth_provider')->nullable();
                $table->index('auth_provider');
                $table->index('auth_provider_id');
            });
        }

        if (Schema::hasTable('settings')) {
            \DB::table('settings')->insert(array (
                    array (
                        'key' => 'social-login.facebook_client_id',
                        'display_name' => 'Facebook client ID',
                        'value' => '',
                        'type' => 'text',
                        'order' => 70,
                        'group' => 'Social login',
                    ),
                    array (
                        'key' => 'social-login.facebook_secret',
                        'display_name' => 'Facebook client secret',
                        'value' => '',
                        'type' => 'text',
                        'order' => 118,
                        'group' => 'Social login',
                    ),

                    array (
                        'key' => 'social-login.twitter_client_id',
                        'display_name' => 'Twitter client ID',
                        'value' => '',
                        'type' => 'text',
                        'order' => 119,
                        'group' => 'Social login',
                    ),
                    array (
                        'key' => 'social-login.twitter_secret',
                        'display_name' => 'Twitter client secret',
                        'value' => '',
                        'type' => 'text',
                        'order' => 120,
                        'group' => 'Social login',
                    ),

                    array (
                        'key' => 'social-login.google_client_id',
                        'display_name' => 'Google client ID',
                        'value' => '',
                        'type' => 'text',
                        'order' => 121,
                        'group' => 'Social login',
                    ),
                    array (
                        'key' => 'social-login.google_secret',
                        'display_name' => 'Google client secret',
                        'value' => '',
                        'type' => 'text',
                        'order' => 122,
                        'group' => 'Social login',
                    ),

                )
            );
        }

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
