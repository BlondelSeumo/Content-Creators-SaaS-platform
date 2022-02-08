<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    public const REQUESTED_STATUS = 'pending';

    public const REJECTED_STATUS = 'rejected';

    public const APPROVED_STATUS = 'verified';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'files', 'status', 'rejectionReason'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /*
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
