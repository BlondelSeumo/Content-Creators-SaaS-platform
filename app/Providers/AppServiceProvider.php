<?php

namespace App\Providers;

use App\Model\UserVerify;
use App\Model\Withdrawal;
use App\Observers\UserVerifyObserver;
use App\Observers\WithdrawalsObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! InstallerServiceProvider::checkIfInstalled()) {
            return false;
        }
        UserVerify::observe(UserVerifyObserver::class);
        Withdrawal::observe(WithdrawalsObserver::class);
        Schema::defaultStringLength(191);
    }
}
