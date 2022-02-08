<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class V106 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // User model related updates
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('identity_verified_at')->nullable()->after('email_verified_at');
                $table->dropColumn('credit');
            });
        }

        // Delete old
        DB::table('data_rows')
            ->where('id', 144)
            ->delete();


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
