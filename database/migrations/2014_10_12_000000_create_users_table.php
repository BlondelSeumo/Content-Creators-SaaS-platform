<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->float('credit')->default(0);
            $table->text('bio')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('public_profile')->default(true);
            $table->float('profile_access_price')->default(5);
            $table->float('profile_access_price_6_months')->default(5);
            $table->float('profile_access_price_12_months')->default(5);
            $table->string('billing_address')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
