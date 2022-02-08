<?php

namespace App\Providers;

use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Hash;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    /**
     * Function used to create an user
     * Used in the register function & installer process.
     *
     * @param $data
     * @return mixed
     */
    public static function createUser($data)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => 'u'.time(),
            'password' => isset($data['password']) ? Hash::make($data['password']) : '',
            'settings' => collect([
                'notification_email_new_sub' => 'true',
                'notification_email_new_message' => 'true',
                'notification_email_expiring_subs' => 'true',
                'notification_email_renewals' => 'false',
                'notification_email_new_tip' => 'true',
                'notification_email_new_comment' => 'false',
                'locale' => getSetting('site.default_site_language'),
            ]),
        ];
        if (isset($data['role_id'])) {
            $userData['role_id'] = $data['role_id'];
        }
        if (isset($data['email_verified_at'])) {
            $userData['email_verified_at'] = $data['email_verified_at'];
        }

        if (isset($data['auth_provider'])) {
            $userData['auth_provider'] = $data['auth_provider'];
        }
        if (isset($data['auth_provider_id'])) {
            $userData['auth_provider_id'] = $data['auth_provider_id'];
        }

        $user = User::create($userData);

        if ($user != null) {
            GenericHelperServiceProvider::createUserWallet($user);
            ListsHelperServiceProvider::createUserDefaultLists($user->id);
        }

        return $user;
    }
}
