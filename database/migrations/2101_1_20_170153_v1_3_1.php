<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V131 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('settings')) {

            DB::table('settings')
                ->where('key', 'emails.driver')
                ->update(['details' => '{
"default" : "log",
"options" : {
"log": "Log",
"sendmail": "Sendmail",
"smtp": "SMTP",
"mailgun": "Mailgun"
}
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
