<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Model\Subscription;
use App\Model\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SubscriptionsController extends Controller
{
    protected $paymentHelper;

    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Method used for canceling an active subscription.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $subscriptionId = $request->subscriptionId;
            if ($subscriptionId != null) {
                $subscription = Subscription::query()->where('id', intval($subscriptionId))->first();
                if ($subscription != null) {
                    if ($subscription->status === Subscription::CANCELED_STATUS) {
                        return Redirect::route('my.settings', ['type' => 'subscriptions'])
                            ->with('error', __('This subscription is already canceled.'));
                    }

                    if ($subscription->provider != null) {
                        if ($subscription->provider === Transaction::PAYPAL_PROVIDER && $subscription->paypal_agreement_id != null) {
                            $this->paymentHelper->cancelPaypalAgreement($subscription->paypal_agreement_id);
                        } elseif ($subscription->provider === Transaction::STRIPE_PROVIDER && $subscription->stripe_subscription_id != null) {
                            $this->paymentHelper->cancelStripeSubscription($subscription->stripe_subscription_id);
                        }
                    }

                    // handle cancel subscription
                    $subscription->status = Subscription::CANCELED_STATUS;
                    $subscription->canceled_at = new \DateTime();

                    $subscription->save();
                }
            }
        } catch (\Exception $exception) {
            // show proper error message
            return Redirect::route('my.settings', ['type' => 'subscriptions'])
                ->with('error', $exception->getMessage());
        }

        return Redirect::route('my.settings', ['type' => 'subscriptions'])
            ->with('success', __('Successfully canceled subscription'));
    }
}
