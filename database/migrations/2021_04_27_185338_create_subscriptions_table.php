<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_user_id');
            $table->unsignedBigInteger('recipient_user_id');
            $table->foreign('sender_user_id')->references('id')->on('users');
            $table->foreign('recipient_user_id')->references('id')->on('users');
            $table->string('paypal_agreement_id')->nullable();
            $table->index('paypal_agreement_id');
            $table->string('stripe_subscription_id')->nullable();
            $table->index('stripe_subscription_id');
            $table->string('paypal_plan_id')->nullable();
            $table->string('type');
            $table->index('type');
            $table->string('provider');
            $table->index('provider');
            $table->string('status');
            $table->index('status');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->float('amount')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
