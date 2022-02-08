<?php

namespace App\Providers;

use App\Model\Notification;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Transaction;
use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Pusher\Pusher;
use Ramsey\Uuid\Uuid;

class NotificationServiceProvider extends ServiceProvider
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
     * Creates a notification payload and broadcasts it.
     *
     * @param $type
     * @param null $toUser
     * @param null $post
     * @param null $postComment
     * @param null $subscription
     * @param null $transaction
     * @param null $reaction
     * @param null $withdrawal
     * @param null $userMessage
     * @return |null
     */
    public static function createAndPublishNotification(
        $type,
        $toUser = null,
        $post = null,
        $postComment = null,
        $subscription = null,
        $transaction = null,
        $reaction = null,
        $withdrawal = null,
        $userMessage = null
    ) {
        try {
            // generate unique id for notification
            do {
                $id = Uuid::uuid4()->getHex();
            } while (Notification::query()->where('id', $id)->first() != null);

            $notificationData = [];
            $notificationData['id'] = $id;
            $notificationData['from_user_id'] = Auth::id();
            $notificationData['type'] = $type;
            $notificationData['to_user_id'] = null;

            if ($post != null && isset($post->id) && isset($post->user_id)) {
                $notificationData['post_id'] = $post->id;
                $notificationData['message'] = __('post notification');
                $notificationData['to_user_id'] = $post->user_id;
            }

            // New post comment
            if ($postComment != null && isset($postComment->id) && isset($postComment->message) && isset($postComment->post_id)) {
                $post = Post::query()->where('id', $postComment->post_id)->first();
                App::setLocale($post->user->settings['locale']); // Setting the locale of the message receiver
                // Building up the notification message to be broadcasted & db saved
                if ($post != null) {
                    $fromUser = User::query()->where('id', $postComment->user_id)->first();
                    if ($fromUser != null) {
                        $notificationData['message'] = __(':name added a new comment on your post', ['name'=>$fromUser->name]);
                    }
                    $notificationData['post_comment_id'] = $postComment->id;
                    $notificationData['to_user_id'] = $post->user_id;
                }
                // Sending the user email notification
                $user = User::where('id', $post->user_id)->select(['email', 'name', 'settings'])->first();
                if (isset($user->settings['notification_email_new_comment']) && $user->settings['notification_email_new_comment'] == 'true') {
                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $user->email,
                            'subject' => __('New comment received'),
                            'title' => __('Hello, :name,', ['name'=>$user->name]),
                            'content' =>  __("You've received a new comment on one of your posts at :siteName.", ['siteName'=>getSetting('site.name')]),
                            'button' => [
                                'text' => __('Your notifications'),
                                'url' => route('my.notifications'),
                            ],
                        ]
                    );
                }
            }

            // New subscription
            if ($subscription != null && isset($subscription->id) && isset($subscription->sender_user_id)
                && isset($subscription->recipient_user_id)) {
                $notificationData['subscription_id'] = $subscription->id;
                $notificationData['to_user_id'] = $subscription->recipient_user_id;
                $notificationData['from_user_id'] = $subscription->sender_user_id;
                // Setting the locale of the message receiver
                $user = User::where('id', $subscription->recipient_user_id)->select(['email', 'name', 'settings'])->first();
                App::setLocale($user->settings['locale']);
                // Building up the notification message to be broadcasted & db saved
                $subscriber = User::query()->where('id', $subscription->sender_user_id)->first();
                if ($subscriber != null) {
                    $notificationData['message'] = __('New subscription from :name', ['name'=>$subscriber->name]);
                } else {
                    $notificationData['message'] = __('A new user subscribed to your profile');
                }
                ListsHelperServiceProvider::managePredefinedUserMemberList($subscription->sender_user_id, $subscription->recipient_user_id, 'follow'); // TODO: Inspect
                // Sending the user email notification
                if (isset($user->settings['notification_email_new_sub']) && $user->settings['notification_email_new_sub'] == 'true') {
                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $user->email,
                            'subject' => __('You got a new subscriber!'),
                            'title' => __('Hello, :name,', ['name'=>$user->name]),
                            'content' => __('You got a new subscriber! You can see more details over your subscriptions tab.'),
                            'button' => [
                                'text' => __('Manage your subs'),
                                'url' => route('my.settings', ['type' => 'subscriptions']),
                            ],
                        ]
                    );
                }
            }

            // New tip
            if ($transaction != null && isset($transaction->id) && isset($transaction->sender_user_id)
                && isset($transaction->amount) && isset($transaction->currency) && isset($transaction->recipient_user_id)) {
                $notificationData['transaction_id'] = $transaction->id;
                $notificationData['to_user_id'] = $transaction->recipient_user_id;
                // Setting the locale of the message receiver
                $user = User::where('id', $transaction->recipient_user_id)->select(['email', 'username', 'name', 'settings'])->first();
                App::setLocale($user->settings['locale']);
                // Building up the notification message to be broadcasted & db saved
                $sender = User::query()->where('id', $transaction->sender_user_id)->first();
                if ($sender != null) {
                    $notificationData['message'] = $sender->name.' '.__('sent you a tip of').' '.$transaction->amount.$transaction->currency.'.';
                }
                // Sending the user email notification
                if (isset($user->settings['notification_email_new_tip']) && $user->settings['notification_email_new_tip'] == 'true') {
                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $user->email,
                            'subject' => __('You got a new tip!'),
                            'title' => __('Hello, :name,', ['name'=>$user->name]),
                            'content' => $notificationData['message'],
                            'button' => [
                                'text' => __('Your notifications'),
                                'url' => route('my.notifications', ['type'=>'subscriptions']),
                            ],
                        ]
                    );
                }
            }

            // New post / comment reaction
            if ($reaction != null && isset($reaction->id) && isset($reaction->user_id)) {
                $user = User::query()->where('id', $reaction->user_id)->first();
                // Post reaction
                if ($user != null) {
                    if (isset($reaction->post_id)) {
                        $post = Post::query()->where('id', $reaction->post_id)->first();
                        if ($post != null) {
                            // Setting the locale of the message receiver
                            $toUser = User::where('id', $post->user_id)->select(['email', 'username', 'name', 'settings'])->first();
                            App::setLocale($user->settings['locale']);
                            // Building up the notification message to be broadcasted & db saved
                            $notificationData['message'] = __(':name liked your post', ['name'=>$user->name]);
                            $notificationData['post_id'] = $post->id;
                            $notificationData['to_user_id'] = $post->user_id;
                        }
                    }
                    // Post comment reaction
                    if (isset($reaction->post_comment_id)) {
                        $postComment = PostComment::query()->where('id', $reaction->post_comment_id)->first();
                        if ($postComment != null) {
                            // Setting the locale of the message receiver
                            $toUser = User::where('id', $postComment->user_id)->select(['email', 'username', 'name', 'settings'])->first();
                            App::setLocale($user->settings['locale']);
                            // Building up the notification message to be broadcasted & db saved
                            $notificationData['message'] = __(':name liked your comment', ['name'=>$user->name]);
                            $notificationData['post_comment_id'] = $postComment->id;
                            $notificationData['to_user_id'] = $postComment->user_id;
                        }
                    }
                }
                $notificationData['reaction_id'] = $reaction->id;
            }

            // Withdrawal request
            if ($withdrawal != null && isset($withdrawal->id) && isset($withdrawal->user_id) && isset($withdrawal->amount)
                && isset($withdrawal->status)) {
                // Setting the locale of the message receiver
                $toUser = User::where('id', $withdrawal->user_id)->select(['email', 'username', 'name', 'settings'])->first();
                App::setLocale($toUser->settings['locale']);
                // Building up the notification message to be broadcasted & db saved
                $notificationData['withdrawal_id'] = $withdrawal->id;
                $notificationData['to_user_id'] = $withdrawal->user_id;
                $notificationData['message'] = __('Withdrawal processed', [
                    'currencySymbol' => SettingsServiceProvider::getWebsiteCurrencySymbol(),
                    'amount' => $withdrawal->amount,
                    'status' =>  $withdrawal->status,
                ]);
            }

            // New user message
            if ($userMessage != null && isset($userMessage->id) && isset($userMessage->sender_id) && isset($userMessage->receiver_id)
                && isset($userMessage->message)) {
                $notificationData['user_message_id'] = $userMessage->id;
                $notificationData['to_user_id'] = $userMessage->receiver_id;
                $notificationData['message'] = $userMessage->message;
            }

            if ($toUser == null && $notificationData['to_user_id'] == null) {
                return null;
            }

            if ($toUser != null && isset($toUser->id) && $notificationData['to_user_id'] == null) {
                $notificationData['to_user_id'] = $toUser->id;
            }

            $toUser = User::query()->where('id', $notificationData['to_user_id'])->first();
            if ($toUser != null) {
                $modelData = $notificationData;
                unset($modelData['message']);
                $notification = Notification::create($modelData);
                $notification->setAttribute('message',$notificationData['message']);
                self::publishNotification($notification, $toUser);
            }
        } catch (\Exception $exception) {
            Log::error('Failed sending notification: '.$exception->getMessage());
        }
    }

    /**
     * Dispatches the notification to puser.
     *
     * @param $notification
     * @param $toUser
     */
    private static function publishNotification($notification, $toUser)
    {
        try {
            $options = [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ];
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                $options
            );
            $data['message'] = $notification->message;
            $data['type'] = $notification->type;
            $data['notification'] = $notification;
            $pusher->trigger($toUser->username, 'new-notification', $data);
        } catch (GuzzleException $guzzleException) {
            Log::error('Pusher guzzle exception: '.$guzzleException->getMessage());
        } catch (\Exception $exception) {
            Log::error('Pusher exception: '.$exception->getMessage());
        }
    }

    /**
     * Dispatches a reaction notification.
     *
     * @param $reaction
     * @return |null
     */
    public static function createNewReactionNotification($reaction)
    {
        $skip = false;
        if ($reaction->post_id != null) {
            $post = Post::query()->where('id', $reaction->post_id)->first();
            if ($post != null && $post->user_id === $reaction->user_id) {
                $skip = true;
            }
        }

        if ($reaction->post_comment_id != null) {
            $postComment = PostComment::query()->where('id', $reaction->post_comment_id)->first();
            if ($postComment != null && $postComment->user_id === $reaction->user_id) {
                $skip = true;
            }
        }

        if (! $skip) {
            return self::createAndPublishNotification(
                Notification::NEW_REACTION,
                null,
                null,
                null,
                null,
                null,
                $reaction
            );
        }
    }

    /**
     * Dispatches a new post comment notification.
     *
     * @param $reaction
     * @return |null
     */
    public static function createNewPostCommentNotification($postComment)
    {
        return self::createAndPublishNotification(
            Notification::NEW_COMMENT,
            null,
            null,
            $postComment,
            null,
            null,
            null
        );
    }

    /**
     * Dispatches a new sub notification.
     *
     * @param $reaction
     * @return |null
     */
    public static function createNewSubscriptionNotification($subscription)
    {
        return self::createAndPublishNotification(
            Notification::NEW_SUBSCRIPTION,
            null,
            null,
            null,
            $subscription,
            null,
            null
        );
    }

    /**
     * Dispatches a new tip notification.
     *
     * @param $reaction
     * @return |null
     */
    public static function createNewTipNotification($transaction)
    {
        return self::createAndPublishNotification(
            Notification::NEW_TIP,
            null,
            null,
            null,
            null,
            $transaction,
            null
        );
    }

    /**
     * Dispatches a withdrawal request change notification.
     *
     * @param $reaction
     * @return |null
     */
    public static function createApprovedOrRejectedWithdrawalNotification($withdrawal)
    {
        return self::createAndPublishNotification(
            Notification::WITHDRAWAL_ACTION,
            null,
            null,
            null,
            null,
            null,
            null,
            $withdrawal
        );
    }

    /**
     * Dispatches a new message notification.
     * @param $userMessage
     * @return |null
     */
    public static function createNewUserMessageNotification($userMessage)
    {
        return self::createAndPublishNotification(
            Notification::NEW_MESSAGE,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $userMessage
        );
    }

    /**
     * Dispatches a sub renewal notification.
     * @param $userMessage
     * @return |null
     */
    public static function sendSubscriptionRenewalEmailNotification($subscription, $succeeded)
    {
        if ($subscription != null) {
            if ($subscription->subscriber != null && $subscription->creator != null) {
                // send email for the user who initiated the subscription
                if (isset($subscription->subscriber->settings['notification_email_renewals'])
                    && $subscription->subscriber->settings['notification_email_renewals'] == 'true') {
                    $message = $succeeded ? __('successfully renewed') : __('failed renewing');
                    $buttonText = $succeeded ? __('Check out his profile for more content') : __('Go back to the website');
                    $buttonUrl = $succeeded ? route('profile', ['username' => $subscription->creator->username]) : route('home');

                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $subscription->subscriber->email,
                            'subject' => __('Your subscription renewal'),
                            'title' => __('Hello, :name,', ['name'=>$subscription->subscriber->name]),
                            'content' =>  __('Email subscription updated', ['name'=>$subscription->creator->name, 'message'=>$message]),
                            'button' => [
                                'text' => $buttonText,
                                'url' => $buttonUrl,
                            ],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Generate new tip notification
     * @param $transaction
     */
    public static function createTipNotificationByTransaction($transaction){
        if ($transaction != null && $transaction->status === Transaction::APPROVED_STATUS && $transaction->type === Transaction::TIP_TYPE) {
            self::createNewTipNotification($transaction);
        }
    }
}
