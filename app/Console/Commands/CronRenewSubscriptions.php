<?php

namespace App\Console\Commands;

use App\Helpers\PaymentHelper;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentsServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CronRenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:renew_subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process subscriptions renewal (update status, add/remove credit, etc)';

    public $paymentHelper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
        parent::__construct();
    }

    /**
     * Process subscriptions renewal (update status, add/remove credit, etc).
     *
     * @return mixed
     */
    public function handle()
    {
        Log::channel()->info('[*]['.date('H:i:s')."] Processing expired subscriptions.\r\n");

        $activeSubscriptions = Subscription::with('subscriber', 'creator')
            ->where('expires_at', '<=', new \DateTime())
            ->where('status', '=', Subscription::ACTIVE_STATUS)
            ->get();

        if (count($activeSubscriptions) < 1) {
            Log::channel('cronjobs')->info('[*]['.date('H:i:s')."] No subscriptions to renew.\r\n");

            return;
        }

        foreach ($activeSubscriptions as $subscription) {
            try{
                $paymentSucceeded = false;
                if ($subscription->provider === Transaction::CREDIT_PROVIDER) {
                    // check if user have enough credit to renew subscription
                    $subscriber = $subscription->subscriber;
                    if ($subscriber != null) {
                        $userWallet = $subscriber->wallet;
                        if ($userWallet != null) {
                            if ($userWallet != null && $userWallet->total >= $subscription->amount) {
                                $paymentSucceeded = true;
                            }
                        }
                    }

                    // if user don't have enough money to renew this subscription set status in suspended
                    if (! $paymentSucceeded) {
                        $subscription->status = Subscription::SUSPENDED_STATUS;
                    }

                    $renewalTransaction = $this->paymentHelper->createSubscriptionRenewalTransaction($subscription, $paymentSucceeded);
                    if ($renewalTransaction != null && $renewalTransaction->status === Transaction::APPROVED_STATUS) {
                        if ($subscription->status !== Subscription::ACTIVE_STATUS) {
                            $subscription->status = Subscription::ACTIVE_STATUS;
                        }
                        $subscription->expires_at = new \DateTime('+'.PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($subscription->type).' month', new \DateTimeZone('UTC'));
                    }
                    $subscription->save();
                } else {
                    // for paypal & stripe subscriptions that were not yet renewed by the payment provider webhook
                    // set status in suspended / send notification to user
                    $this->paymentHelper->createSubscriptionRenewalTransaction($subscription, false);

                    $subscription->status = Subscription::EXPIRED_STATUS;
                    $subscription->save();
                }

                NotificationServiceProvider::sendSubscriptionRenewalEmailNotification($subscription, $paymentSucceeded);

                Log::channel('cronjobs')->info('[*]['.date('H:i:s').'] Successfully processed subscription:'.$subscription->id.".\r\n");
            } catch (\Exception $exception){
                Log::channel('cronjobs')->info('[*]['.date('H:i:s')."] Error processing subscription ".$subscription->id." error: ".$exception->getMessage());
            }
        }

        Log::channel('cronjobs')->info('[*]['.date('H:i:s')."] Finished processing subscriptions renew.\r\n");
    }
}
