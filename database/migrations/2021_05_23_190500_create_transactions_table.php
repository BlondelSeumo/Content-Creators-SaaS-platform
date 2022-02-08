<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     * @@ -13,17 +13,19 @@ class CreateTransactionsTable extends Migration
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_user_id');
            $table->unsignedBigInteger('recipient_user_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('sender_user_id')->references('id')->on('users');
            $table->foreign('recipient_user_id')->references('id')->on('users');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->string('stripe_transaction_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('paypal_transaction_id')->nullable();
            $table->string('paypal_transaction_token')->nullable();
            $table->string('status');
            $table->string('type');
            $table->string('payment_provider');
            $table->string('currency');
            $table->string('paypal_payer_id')->nullable();
            $table->float('amount');
            $table->text('taxes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
