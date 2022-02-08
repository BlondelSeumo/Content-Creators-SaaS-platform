<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public const MESSAGES_FILTER = 'messages';

    public const LIKES_FILTER = 'likes';

    public const SUBSCRIPTIONS_FILTER = 'subscriptions';

    public const TIPS_FILTER = 'tips';

    public const PROMOS_FILTER = 'promos';

    public $notificationTypes = [
        self::MESSAGES_FILTER,
        self::LIKES_FILTER,
        self::SUBSCRIPTIONS_FILTER,
        self::TIPS_FILTER,
        self::PROMOS_FILTER,
    ];

    public const NEW_TIP = 'tip';

    public const NEW_REACTION = 'reaction';

    public const NEW_COMMENT = 'new-comment';

    public const NEW_SUBSCRIPTION = 'new-subscription';

    public const WITHDRAWAL_ACTION = 'withdrawal-action';

    public const NEW_MESSAGE = 'new-message';

    // Disable auto incrementing as we set the id manually (uuid)
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id', 'post_id', 'to_user_id', 'type', 'id', 'subscription_id', 'transaction_id',
        'reaction_id', 'post_comment_id', 'withdrawal_id', 'user_message_id', 'message'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

    /*
     * Relationships
     */

    public function fromUser()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\User', 'to_user_id');
    }

    public function post()
    {
        return $this->belongsTo('App\Model\Post', 'post_id');
    }

    public function postComment()
    {
        return $this->belongsTo('App\Model\PostComment', 'post_comment_id');
    }

    public function subscription()
    {
        return $this->belongsTo('App\Model\Subscription', 'subscription_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Model\Transaction', 'transaction_id');
    }

    public function reaction()
    {
        return $this->belongsTo('App\Model\Reaction', 'reaction_id');
    }

    public function withdrawal()
    {
        return $this->belongsTo('App\Model\Withdrawal', 'withdrawal_id');
    }

    public function userMessage()
    {
        return $this->belongsTo('App\Model\UserMessage', 'user_message_id');
    }
}
