<?php

namespace App\Providers;

use App\Model\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\Uuid;

class GenericHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Check if user meets all ID verification steps.
     *
     * @return bool
     */
    public static function isUserVerified()
    {
        if (
        (Auth::user()->verification && Auth::user()->verification->status == 'verified') &&
        Auth::user()->birthdate &&
        Auth::user()->email_verified_at
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates a default wallet for a user.
     * @param $user
     */
    public static function createUserWallet($user)
    {
        try {
            $userWallet = Wallet::query()->where('user_id', $user->id)->first();
            if ($userWallet == null) {
                // generate unique id for wallet
                do {
                    $id = Uuid::uuid4()->getHex();
                } while (Wallet::query()->where('id', $id)->first() != null);

                Wallet::create([
                    'id' => $id,
                    'user_id' => $user->id,
                    'total' => 0.0,
                    'paypal_balance' => 0.0,
                    'stripe_balance' => 0.0,
                ]);
            }
        } catch (\Exception $exception) {
            Log::error('User wallet creation error: '.$exception->getMessage());
        }
    }
}
