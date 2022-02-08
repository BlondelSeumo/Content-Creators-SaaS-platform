<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V150 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        #
        # Adding back foreign keys on delete cascade behaviour
        #

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign('subscriptions_sender_user_id_foreign');
                $table->dropForeign('subscriptions_recipient_user_id_foreign');
                $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                $table->dropForeign('withdrawals_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('user_verifies')) {
            Schema::table('user_verifies', function (Blueprint $table) {
                $table->dropForeign('user_verifies_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('user_reports')) {
            Schema::table('user_reports', function (Blueprint $table) {
                $table->dropForeign('user_reports_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign('transactions_sender_user_id_foreign');
                $table->dropForeign('transactions_recipient_user_id_foreign');
                $table->dropForeign('transactions_subscription_id_foreign');
                $table->dropForeign('transactions_post_id_foreign');
                $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                $table->dropForeign('withdrawals_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
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
