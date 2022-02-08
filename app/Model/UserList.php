<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    public const FOLLOWERS_TYPE = 'followers';

    public const BLOCKED_TYPE = 'blocked';

    public const CUSTOM_TYPE = 'custom';

    public $notificationTypes = [
        self::FOLLOWERS_TYPE,
        self::BLOCKED_TYPE,
        self::CUSTOM_TYPE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'type',
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

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->with(['posts']);
    }

    public function members()
    {
        return $this->hasMany('App\Model\UserListMember', 'list_id');
    }
}
