<?php

namespace App\Providers;

use App\Model\Attachment;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Reaction;
use App\Model\Subscription;
use App\Model\Transaction;
use App\User;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
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
    }

    /**
     * Get admin dashboard total posts count
     * @return int
     */
    public static function getPostsCount()
    {
        return Post::all()->count();
    }

    /**
     * Get admin dashboard total post attachments count
     * @return int
     */
    public static function getPostAttachmentsCount()
    {
        return Attachment::query()->whereNotNull('post_id')->count();
    }

    /**
     * Get admin dashboard total post comments count
     * @return int
     */
    public static function getPostCommentsCount()
    {
        return PostComment::all()->count();
    }

    /**
     * Get admin dashboard total reactions count
     * @return int
     */
    public static function getReactionsCount()
    {
        return Reaction::query()->whereNotNull('post_id')->count();
    }

    /**
     * Get admin dashboard active subscriptions count
     * @return int
     * @throws \Exception
     */
    public static function getActiveSubscriptionsCount()
    {
        return Subscription::query()->where('expires_at', '>=', new \DateTime('now', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard total transactions count
     * @return int
     */
    public static function getTotalTransactionsCount()
    {
        return Transaction::all()->count();
    }

    /**
     * Get admin dashboard last 24 hours registered users count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursRegisteredUsersCount(){
        return User::query()->where('created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard last 24 hours total earned
     * @return mixed
     * @throws \Exception
     */
    public static function getLast24HoursTotalEarned(){
        return Transaction::query()
            ->where([
                ['created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC'))],
                ['status', '=', Transaction::APPROVED_STATUS]
            ])
            ->sum('amount');
    }

    /**
     * Get admin dashboard last 24 hours subscriptions count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursSubscriptionsCount(){
        return Subscription::query()->where('created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard last 24 hours posts count
     * @return int
     * @throws \Exception
     */
    public static function getLast24HoursPostsCount(){
        return Post::query()->where('created_at', '>=', new \DateTime('-1 day', new \DateTimeZone('UTC')))->count();
    }

    /**
     * Get admin dashboard total subscriptions revenue
     * @return mixed
     * @throws \Exception
     */
    public static function getTotalSubscriptionsRevenue(){
        return Transaction::query()->where('status', '=', Transaction::APPROVED_STATUS)->whereNotNull('subscription_id')->sum('amount');
    }

    /**
     * Get admin dashboard total earned
     * @return mixed
     */
    public static function getTotalEarned(){
        return Transaction::query()
            ->where('status', '=', Transaction::APPROVED_STATUS)
            ->sum('amount');
    }
}
