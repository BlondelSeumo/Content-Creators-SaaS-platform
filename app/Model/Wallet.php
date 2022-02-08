<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Wallet extends Model
{
    // Disable auto incrementing as we set the id manually (uuid)
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'total', 'paypal_balance', 'stripe_balance',
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

    protected $appends = ['pendingBalance'];

    /*
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /*
     * Virtual attributes
     */

    public function getPendingBalanceAttribute()
    {
        return Withdrawal::query()->where(['user_id' => Auth::user()->id, 'status' => Withdrawal::REQUESTED_STATUS])->sum('amount');
    }
}
