<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V120 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Add coinbase related column for transactions table
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('coinbase_charge_id')->nullable()->after('paypal_transaction_token');
                $table->string('coinbase_transaction_token')->after('coinbase_charge_id')->nullable();
            });
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')->insert(array(
                0 =>
                    array (
                        'key' => 'payments.coinbase_api_key',
                        'display_name' => 'CoinBase Api Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 35,
                        'group' => 'Payments',
                    ),
                1 =>
                    array (
                        'key' => 'payments.coinbase_webhook_key',
                        'display_name' => 'CoinBase Webhooks Secret',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 36,
                        'group' => 'Payments',
                    )
            ));


            // Settings updates (Fixing broken JSONs)
            DB::table('settings')
                ->where('key', 'site.hide_identity_checks')
                ->update(['details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "If enabled, users the \'Verify\' user setting menu will be hidden by default."
}'
                ]);

            DB::table('settings')
                ->where('key', 'site.enforce_user_identity_checks')
                ->update(['details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description" : "If enabled, users will only be able to post content if verified."
}'
                ]);

            DB::table('settings')
                ->where('key', 'feed.suggestions_skip_empty_profiles')
                ->update(['details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "If enabled, users will only get suggestions of profiles that have both avatar and cover filled in."
}'
                ]);

            DB::table('settings')
                ->where('key', 'site.allow_pwa_installs')
                ->update(['details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "Turns the site into an installable PWA on all devices. Website must be server from a root domain."
}'
                ]);

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
