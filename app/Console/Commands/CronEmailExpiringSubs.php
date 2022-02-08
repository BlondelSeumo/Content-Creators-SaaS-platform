<?php

namespace App\Console\Commands;

use App\Model\Subscription;
use App\Providers\EmailsServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CronEmailExpiringSubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:email_expiring_subs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emails users about soon to expire subs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Emails users about soon to expire subs.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."]  Starting: Expiring email subs..\r\n");

        // Subs to ne re-newed in the upcoming 24h
        $renewalSubs = Subscription::with('subscriber', 'creator')
            ->whereRaw('HOUR(TIMEDIFF(expires_at,now() )) <= 24')
            ->whereRaw('expires_at < now() + INTERVAL 24 HOUR')
            ->where('paypal_agreement_id', null)
            ->where('stripe_subscription_id', null)
            ->where('paypal_plan_id', null)
            ->get();

        foreach ($renewalSubs as $subToRenew) {
            if (isset($subToRenew->subscriber->settings['notification_email_expiring_subs']) && $subToRenew->subscriber->settings['notification_email_expiring_subs'] == 'true') {
                App::setLocale($subToRenew->subscriber->settings['locale']);
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $subToRenew->subscriber->email,
                        'subject' => __('Expiring subscription'),
                        'title' => __('Hello, :subscriberName,', ['subscriberName' => $subToRenew->subscriber->name]),
                        'content' => __('Your subscription to :creatorName is about to expire in the next 24h hours. Please top up your credit in order to keep your subscription going.', ['subscriberName' => $subToRenew->creator->name]),
                        'button' => [
                            'text' => __('Manage your subs'),
                            'url' => route('my.settings', ['type' => 'subscriptions']),
                        ],
                    ]
                );
            }
        }

        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."]  Expiring email subs sent successfully..\r\n");
    }
}
