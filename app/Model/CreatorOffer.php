<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CreatorOffer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'old_profile_access_price',
        'old_profile_access_price_6_months',
        'old_profile_access_price_12_months',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
      'expires_at' => 'date',
    ];

    /*
     * Relationships
     */

    public function creator()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
