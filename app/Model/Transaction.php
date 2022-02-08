<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const PENDING_STATUS = 'pending';

    public const CANCELED_STATUS = 'canceled';

    public const APPROVED_STATUS = 'approved';

    public const DECLINED_STATUS = 'declined';

    public const REFUNDED_STATUS = 'refunded';

    public const INITIATED_STATUS = 'initiated';

    public const TIP_TYPE = 'tip';

    public const POST_UNLOCK = 'post-unlock';

    public const DEPOSIT_TYPE = 'deposit';

    public const ONE_MONTH_SUBSCRIPTION = 'one-month-subscription';

    public const THREE_MONTHS_SUBSCRIPTION = 'three-months-subscription';

    public const SIX_MONTHS_SUBSCRIPTION = 'six-months-subscription';

    public const YEARLY_SUBSCRIPTION = 'yearly-subscription';

    public const SUBSCRIPTION_RENEWAL = 'subscription-renewal';

    public const PAYPAL_PROVIDER = 'paypal';

    public const STRIPE_PROVIDER = 'stripe';

    public const MANUAL_PROVIDER = 'manual';

    public const CREDIT_PROVIDER = 'credit';

    public const COINBASE_PROVIDER = 'coinbase';

    public const COINASE_API_BASE_PATH = 'https://api.commerce.coinbase.com';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_user_id', 'recipient_user_id', 'subscription_id', 'stripe_transaction_id', 'paypal_payer_id', 'post_id',
        'paypal_transaction_id', 'status', 'type', 'amount', 'payment_provider', 'paypal_transaction_token', 'currency', 'taxes',
        'coinbase_charge_id', 'coinbase_transaction_token'
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

    ];

    /*
     * Relationships
     */

    public function receiver()
    {
        return $this->belongsTo('App\User', 'recipient_user_id');
    }

    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_user_id');
    }

    public function subscription()
    {
        return $this->belongsTo('App\Model\Subscription', 'subscription_id');
    }

    public function post()
    {
        return $this->belongsTo('App\Model\Post', 'post_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Model\Invoice', 'invoice_id');
    }
}
