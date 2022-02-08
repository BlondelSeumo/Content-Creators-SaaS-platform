<?php

namespace App\Console\Commands;

use App\Model\Subscription;
use App\Providers\EmailsServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CronEmailUpcomingRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:email_upcoming_renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emails user about upcoming renewal subscriptions';

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
     * Emails user about upcoming renewal subscriptions.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."] Starting: Upcoming renewals notifications to be sent..\r\n");

        // Subs to ne re-newed in the upcoming 24h
        $renewalSubs = Subscription::with('subscriber', 'creator')
            ->whereRaw('HOUR(TIMEDIFF(expires_at,now() )) <= 24')
            ->whereRaw('expires_at < now() + INTERVAL 24 HOUR')
            ->get();

        foreach ($renewalSubs as $subToRenew) {
            if (isset($subToRenew->subscriber->settings['notification_email_renewals']) && $subToRenew->subscriber->settings['notification_email_renewals'] == 'true') {
                App::setLocale($subToRenew->subscriber->settings['locale']);
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $subToRenew->subscriber->email,
                        'subject' => __('Upcoming renewal'),
                        'title' => __('Hello, :name,', ['name'=>$subToRenew->subscriber->name]),
                        'content' => __('Your subscription to :creatorName is about to renew in the next 24h hours. If your payment settings are up to date, there\'s nothing to do on your end.', ['creatorName' => $subToRenew->creator->name]),
                        'button' => [
                            'text' => __('Manage your subs'),
                            'url' => route('my.settings', ['type' => 'subscriptions']),
                        ],
                    ]
                );
            }
        }

        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."] Upcoming renewals emails sent successfully..\r\n");
    }
}
