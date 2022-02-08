<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type')->index();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->unsignedBigInteger('post_comment_id')->nullable();
            $table->foreign('post_comment_id')->references('id')->on('post_comments')->onDelete('cascade');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->unsignedBigInteger('reaction_id')->nullable();
            $table->foreign('reaction_id')->references('id')->on('reactions')->onDelete('cascade');
            $table->unsignedBigInteger('withdrawal_id')->nullable();
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals')->onDelete('cascade');
            $table->unsignedBigInteger('user_message_id')->nullable()->index();
            $table->foreign('user_message_id')->references('id')->on('user_messages')->onDelete('cascade')->unsigned()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
