<?php

namespace App\Providers;

use App\Mail\GenericEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class EmailsServiceProvider extends ServiceProvider
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

    public static function sendGenericEmail($options = [])
    {
        Mail::to($options['email'])->send(new GenericEmail(
            [
                'subject' => $options['subject'],
                'mailTitle' => $options['title'],
                'mailContent' => $options['content'],
                'button' => [
                    'color' => 'primary',
                    'text' => $options['button']['text'],
                    'url' => $options['button']['url'],
                ],
            ]
        ));

        return true;
    }
}
