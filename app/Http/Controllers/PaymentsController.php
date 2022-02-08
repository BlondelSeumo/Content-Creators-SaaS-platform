<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Http\Requests\CreateTransactionRequest;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Providers\InvoiceServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Stripe\StripeClient;

class PaymentsController extends Controller
{
    protected $paymentHandler;

    /**
     * PaymentsController constructor.
     * @param PaymentsServiceProvider $paymentsProvider
     */
    public function __construct(PaymentHelper $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    /**
     * Initiates the payment based on the required provider.
     * @param CreateTransactionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initiatePayment(CreateTransactionRequest $request)
    {
        $transactionType = $request->get('transaction_type');
        $redirectLink = null;
        // generate one time transaction
        try {
            $this->updateUserBillingDetails($request);

            $transaction = new Transaction();
            $transaction['sender_user_id'] = Auth::user()->id;
            $transaction['recipient_user_id'] = $request->get('recipient_user_id');
            $transaction['post_id'] = $request->get('post_id');
            $transaction['type'] = $transactionType;
            $transaction['status'] = Transaction::INITIATED_STATUS;
            $transaction['amount'] = $request->get('amount');
            $transaction['currency'] = config('app.site.currency_code');
            $transaction['payment_provider'] = $request->get('provider');
            $transaction['taxes'] = $request->get('taxes');

            if ($transaction['amount'] == 0) {
                $errorMessage = __('Something went wrong with this transaction. Please try again');

                return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
            }

            if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                $this->paymentHandler->initiatePaypalContext();
            }

            if ($transaction['payment_provider'] === Transaction::STRIPE_PROVIDER) {
                $redirectLink = $this->paymentHandler->generateStripeSessionByTransaction($transaction);
                // if we cannot fetch a redirect link it means stripe session generation process failed
                if ($redirectLink == null) {
                    return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage = __('Failed generating stripe session'));
                }
            }

            if ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                $userAvailableAmount = $this->paymentHandler->getLoggedUserAvailableAmount();
                // check if user have enough money to pay with credit for this transaction
                if ($userAvailableAmount < $transaction['amount']) {
                    $errorMessage = __("You don't have enough money to pay with credit for this transaction. Please try with another payment method");

                    return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
                }
            }

            switch ($transactionType) {
                case Transaction::TIP_TYPE:
                    if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                        $redirectLink = $this->paymentHandler->initiateOneTimePaypalTransaction($transaction);
                    } elseif ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                        $this->paymentHandler->generateOneTimeCreditTransaction($transaction);
                    } elseif($transaction['payment_provider'] == Transaction::COINBASE_PROVIDER){
                        $redirectLink = $this->paymentHandler->generateCoinBaseTransaction($transaction);
                    }
                    break;
                case Transaction::POST_UNLOCK:
                    if (PostsHelperServiceProvider::userPaidForPost(Auth::user()->id, $transaction['post_id'])) {
                        $errorMessage = __('You already unlocked this post.');

                        return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
                    }

                    if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                        $redirectLink = $this->paymentHandler->initiateOneTimePaypalTransaction($transaction);
                    } elseif ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                        $this->paymentHandler->generateOneTimeCreditTransaction($transaction);
                    } elseif($transaction['payment_provider'] == Transaction::COINBASE_PROVIDER){
                        $redirectLink = $this->paymentHandler->generateCoinBaseTransaction($transaction);
                    }
                    break;
                case Transaction::DEPOSIT_TYPE:
                    $transaction['recipient_user_id'] = Auth::user()->id;
                    if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                        $redirectLink = $this->paymentHandler->initiateOneTimePaypalTransaction($transaction);
                    } elseif($transaction['payment_provider'] == Transaction::COINBASE_PROVIDER){
                        $redirectLink = $this->paymentHandler->generateCoinBaseTransaction($transaction);
                    }
                    break;
                case Transaction::ONE_MONTH_SUBSCRIPTION:
                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                case Transaction::YEARLY_SUBSCRIPTION:
                    if (PostsHelperServiceProvider::hasActiveSub($transaction['sender_user_id'], $transaction['recipient_user_id'])) {
                        $errorMessage = __('You already have an active subscription for this user.');

                        return $this->paymentHandler->redirectByTransaction($transaction, $errorMessage);
                    }

                    if ($transaction['payment_provider'] == Transaction::PAYPAL_PROVIDER) {
                        $redirectLink = $this->paymentHandler->generatePaypalSubscriptionByTransaction($transaction);
                    } elseif ($transaction['payment_provider'] == Transaction::STRIPE_PROVIDER) {
                        $this->paymentHandler->generateStripeSubscriptionByTransaction($transaction);
                    } elseif ($transaction['payment_provider'] == Transaction::CREDIT_PROVIDER) {
                        $this->paymentHandler->generateCreditSubscriptionByTransaction($transaction);
                    }
                    break;
                default:
                    return $this->paymentHandler->redirectByTransaction($transaction);
                    break;
            }
            $transaction->save();

            if ($transaction['payment_provider'] === Transaction::CREDIT_PROVIDER
                && $transaction['status'] === Transaction::APPROVED_STATUS) {
                $this->paymentHandler->creditReceiverForTransaction($transaction);
                $this->paymentHandler->deductMoneyFromUserWalletForCreditTransaction($transaction, Auth::user()->wallet);
                $this->paymentHandler->createNewTipNotificationForCreditTransaction($transaction);
            }

            if ($transaction != null) {
                try {
                    $invoice = InvoiceServiceProvider::createInvoiceByTransaction($transaction);
                    if ($invoice != null) {
                        $transaction->invoice_id = $invoice->id;
                        $transaction->save();
                    }
                } catch (\Exception $exception) {
                    Log::error("Failed generating invoice for transaction: ".$transaction->id." error: ".$exception->getMessage());
                }
            }
        } catch (\Exception $exception) {
            return Redirect::route('feed')
                ->with('error', __('Payment failed.'));
        }

        $paymentProvider = $transaction['payment_provider'];
        if ($paymentProvider == Transaction::PAYPAL_PROVIDER || $paymentProvider == Transaction::STRIPE_PROVIDER
            || $paymentProvider == Transaction::COINBASE_PROVIDER) {
            // Url generated successfully
            if (isset($redirectLink)) {
                // redirect to paypal
                return Redirect::away($redirectLink);
            }

            return $this->paymentHandler->redirectByTransaction($transaction);
        } else {
            return $this->paymentHandler->redirectByTransaction($transaction);
        }
    }

    /**
     * Handles the deposit request response.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function executePaypalPayment(Request $request)
    {
        // Get the payment ID before session clear
        $payment_id = $request->get('paymentId');

        // Checking for valid request
        if (empty($request->get('token'))) {
            return Redirect::route('my.settings', ['type' => 'deposit'])
                ->with('error', __('Looks like the payment process has been cancelled.')); // warning
        }

        // find paypal transaction and update it
        $transaction = Transaction::query()->where(['paypal_transaction_token' => $request->get('token')])->first();
        if ($transaction != null) {
            if ($transaction->type != null) {
                if ($this->paymentHandler->isSubscriptionPayment($transaction->type) && $transaction->subscription_id != null) {
                    $this->paymentHandler->executePaypalAgreementPayment($transaction);
                    $transaction->save();
                } else {
                    if (empty($request->get('PayerID'))) {
                        return $this->paymentHandler->redirectByTransaction($transaction);
                    }

                    $this->paymentHandler->executeOneTimePaypalPayment($request, $transaction, $payment_id);
                    $transaction->save();
                }
            }

            if ($transaction != null && $transaction->status === Transaction::APPROVED_STATUS && $transaction->type === Transaction::TIP_TYPE) {
                NotificationServiceProvider::createNewTipNotification($transaction);
            }
        }

        return $this->paymentHandler->redirectByTransaction($transaction);
    }

    /**
     * Stripe payment confirmation endpoint / webhook.
     */
    public function stripePaymentsHook()
    {
        app('debugbar')->disable();

        $endpoint_secret = getSetting('payments.stripe_webhooks_secret');
        $payload = @file_get_contents('php://input');
        if (isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        } else {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        Log::info('Stripe payload received. Proceeding with completing the payment & fulfill the order.');
        Log::debug($event);

        try {
            if ($event->type === 'checkout.session.completed') {
            // Payment is successful and the subscription is created.
            $session = $event->data->object;
            if ($session->id != null) {
                $this->paymentHandler->updateTransactionByStripeSessionId($session->id);
            }
            // Occurs whenever a customer's subscription ends.
            } elseif ($event->type === 'customer.subscription.deleted' && isset($event->data->object) && $event->data->object->id != null) {
                $subscription = Subscription::query()->where('stripe_subscription_id', $event->data->object->id)->first();
                if ($subscription != null) {
                    $subscription->status = Subscription::CANCELED_STATUS;

                    $subscription->update();
                }
            } elseif (($event->type === 'invoice.paid' || $event->type === 'invoice.payment_failed') && isset($event->data->object)) {
                $paymentSucceeded = $event->type === 'invoice.paid' ? true : false;
                $stripe = new StripeClient(getSetting('payments.stripe_secret_key'));
                $stripeInvoice = $stripe->invoices->retrieve($event->data->object->id);
                if ($stripeInvoice != null && $stripeInvoice->subscription) {
                    $stripeSub = $stripe->subscriptions->retrieve($stripeInvoice->subscription);
                    if ($stripeSub != null && $stripeSub->id != null) {
                        $subscription = Subscription::query()->where('stripe_subscription_id', $stripeSub->id)->first();
                        if ($subscription != null && isset($subscription->expires_at) && $subscription->expires_at < new \DateTime()) {
                            $this->paymentHandler->createSubscriptionRenewalTransaction($subscription, $paymentSucceeded, $event->data->object->id);
                            // update subscription expire date
                            if ($paymentSucceeded) {
                                $subscription->status = Subscription::ACTIVE_STATUS;
                                $date = new \DateTime();
                                $subscription->expires_at = $date->setTimestamp($stripeSub->current_period_end);
                            } else {
                                if ($subscription->expires_at <= new \DateTime()) {
                                    $subscription->status = Subscription::EXPIRED_STATUS;
                                } else {
                                    $subscription->status = Subscription::FAILED_STATUS;
                                }
                            }
                            $subscription->save();
                        }
                    }
                }
            } elseif ($event->type === 'charge.refunded' && isset($event->data->object) && $event->data->object->payment_intent != null) {
                $transaction = Transaction::query()->where('stripe_transaction_id', $event->data->object->payment_intent)->with('subscription')->first();
                if ($transaction) {
                    if($transaction->status === Transaction::APPROVED_STATUS){
                        $this->paymentHandler->deductMoneyFromUserForRefundedTransaction($transaction);
                    }

                    $transaction->status =Transaction::REFUNDED_STATUS;
                    $transaction->save();

                    if($transaction->subscription != null){
                        $transaction->subscription->status = Subscription::SUSPENDED_STATUS;
                        $transaction->subscription->expires_at = new \DateTime('now', new \DateTimeZone('UTC'));
                        $transaction->subscription->save();
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        http_response_code(200);
    }

    /**
     * Gets stripe transaction status and redirects.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getStripePaymentStatus(Request $request)
    {
        $transaction = $this->paymentHandler->updateTransactionByStripeSessionId($request->get('session_id'));
        NotificationServiceProvider::createTipNotificationByTransaction($transaction);

        return $this->paymentHandler->redirectByTransaction($transaction);
    }

    /**
     * Handles Coinbase payment execution
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAndUpdateCoinbaseTransaction(Request $request)
    {
        $coinbaseTransactionToken = $request->get('token');
        $transaction = Transaction::query()->where('coinbase_transaction_token', $coinbaseTransactionToken)->first();
        if ($transaction != null) {
            $this->paymentHandler->checkAndUpdateCoinbaseTransaction($transaction);
            $transaction->save();
        }
        NotificationServiceProvider::createTipNotificationByTransaction($transaction);

        return $this->paymentHandler->redirectByTransaction($transaction);
    }

    /**
     * Handles Coinbase payments hook
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function coinbaseHook(Request $request){
        if(!getSetting('payments.coinbase_webhook_key')){
            return response()->json([
                'status' => 400
            ], 400);
        }

        $payload = json_decode($request->getContent(), true);
        $computedSignature = hash_hmac('sha256', $request->getContent(), getSetting('payments.coinbase_webhook_key'));

        // Validate the webhook signature
        if (hash_equals($computedSignature, $request->server('HTTP_X_CC_WEBHOOK_SIGNATURE'))) {
            Log::info("coinbase payload: ", [$payload]);
            if(isset($payload['event']) && isset($payload['event']['type']) && isset($payload['event']['data']) && isset($payload['event']['data']['id'])){
                if($payload['event']['type'] === 'charge:failed' || $payload['event']['type'] === 'charge:confirmed'){
                    $transaction = Transaction::query()->where('coinbase_charge_id', $payload['event']['data']['id'])->first();
                    if($transaction != null){
                        if($payload['event']['type'] === 'charge:failed'){
                            $transaction->status = Transaction::CANCELED_STATUS;
                            $transaction->save();
                        } else if ($payload['event']['type'] === 'charge:confirmed') {
                            $transaction->status = Transaction::APPROVED_STATUS;
                            $transaction->save();
                            $this->paymentHandler->creditReceiverForTransaction($transaction);
                            NotificationServiceProvider::createTipNotificationByTransaction($transaction);
                        }
                    }
                }
            }
        } else {
            Log::info('Coinbase signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Paypal handling webhook method.
     *
     * @param Request $request
     */
    public function paypalPaymentsHook(Request $request)
    {
        try {
            $webhookContent = json_decode($request->getContent(), true);
            $eventType = $webhookContent['event_type'];
            $cancelStatuses = ['partially_refunded', 'refunded', 'denied'];
            $resourceContent = $webhookContent['resource'];

            Log::info('Paypal payload received. Proceeding with completing the payment & fulfill the order.');
            Log::debug($webhookContent);

            switch ($eventType) {
                case 'PAYMENT.SALE.COMPLETED':
                    // handle recurring payments (one month subscriptions)
                    if (array_key_exists('billing_agreement_id', $resourceContent) && ! empty($resourceContent['billing_agreement_id'])) {
                        $agreementId = $resourceContent['billing_agreement_id'];
                        $this->paymentHandler->verifyPayPalAgreement($agreementId, null, $resourceContent['id']);
                    // handle one time payments
                    } elseif (array_key_exists('parent_payment', $resourceContent) && ! empty($resourceContent['parent_payment']) && empty($resourceContent['state'])) {
                        $transaction = Transaction::query()->where('paypal_transaction_id', $resourceContent['parent_payment'])->first();
                        if ($transaction != null && $transaction->status == Transaction::INITIATED_STATUS) {
                            if ($resourceContent['state'] == 'completed') {
                                $transaction->status = Transaction::APPROVED_STATUS;
                            } elseif (in_array($resourceContent['state'], $cancelStatuses)) {
                                $transaction->status = Transaction::CANCELED_STATUS;
                            } elseif ($resourceContent['state'] == 'pending') {
                                $transaction->status = Transaction::PENDING_STATUS;
                            }

                            $transaction->save();

                            if ($transaction->status == Transaction::APPROVED_STATUS) {
                                $this->paymentHandler->creditReceiverForTransaction($transaction);
                            }
                        }
                    }
                    break;
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    if (isset($resourceContent['id']) && $resourceContent['id'] != null && isset($resourceContent['state']) && $resourceContent['state'] != null) {
                        // find a subscription by this id
                        $subscription = Subscription::query()->where('paypal_agreement_id', $resourceContent['id'])->first();
                        if ($subscription != null) {
                            if ($resourceContent['state'] == 'Cancelled') {
                                $subscription->status = Subscription::CANCELED_STATUS;
                            } elseif ($resourceContent['state'] == 'Suspended') {
                                $subscription->status = Subscription::SUSPENDED_STATUS;
                            } elseif ($resourceContent['state'] == 'Expired') {
                                $subscription->status = Subscription::EXPIRED_STATUS;
                            }

                            $subscription->save();
                        }
                    }
                    break;
                case 'PAYMENT.SALE.REFUNDED':
                    if (array_key_exists('parent_payment', $resourceContent) && ! empty($resourceContent['parent_payment'])) {
                        $transaction = Transaction::query()->where('paypal_transaction_id', $resourceContent['parent_payment'])->with('subscription')->first();
                        if ($transaction) {
                            if($transaction->status === Transaction::APPROVED_STATUS){
                                $this->paymentHandler->deductMoneyFromUserForRefundedTransaction($transaction);
                            }

                            $transaction->status = Transaction::REFUNDED_STATUS;
                            $transaction->save();

                            if($transaction->subscription != null){
                                $transaction->subscription->status = Subscription::SUSPENDED_STATUS;
                                $transaction->subscription->expires_at = new \DateTime('now', new \DateTimeZone('UTC'));
                                $transaction->subscription->save();
                            }
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        http_response_code(200);
    }

    /**
     * Method used for saving user billing details.
     *
     * @param $request
     */
    public function updateUserBillingDetails($request)
    {
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $billingAddress = $request->get('billing_address');
        $country = $request->get('country');
        $city = $request->get('city');
        $state = $request->get('state');
        $postcode = $request->get('postcode');

        // update user billing details if they changed
        if ($firstName != null || $lastName != null || $billingAddress != null) {
            $loggedUserId = Auth::user()->id;
            $loggedUser = User::query()->where('id', $loggedUserId)->first();

            if ($loggedUser != null) {
                if ($firstName != null && $firstName != $loggedUser->first_name) {
                    $loggedUser->first_name = $firstName;
                }

                if ($lastName != null && $lastName != $loggedUser->last_name) {
                    $loggedUser->last_name = $lastName;
                }

                if ($billingAddress != null && $billingAddress != $loggedUser->billing_address) {
                    $loggedUser->billing_address = $billingAddress;
                }

                if ($country != null && $country != $loggedUser->country) {
                    $loggedUser->country = $country;
                }

                if ($state != null && $state != $loggedUser->state) {
                    $loggedUser->state = $state;
                }

                if ($city != null && $city != $loggedUser->city) {
                    $loggedUser->city = $city;
                }

                if ($postcode != null && $postcode != $loggedUser->postcode) {
                    $loggedUser->postcode = $postcode;
                }
                $loggedUser->save();
            }
        }
    }
}
