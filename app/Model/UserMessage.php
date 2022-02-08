<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['sender_id', 'receiver_id', 'message', 'replyTo', 'isSeen'];

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

    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id');
    }

    public static function initialMessages($senderID, $receiverID)
    {
        return self::whereRaw('receiver_id = ? and sender_id = ? and replyTo = 0 ', [$receiverID, $senderID])
            ->orWhereRaw('receiver_id = ? and sender_id = ? and  replyTo = 0 ', [$senderID, $receiverID])
            ->first();
    }

    public function repliesMessages()
    {
        return $this->hasMany('App\UserMessage', 'replyTo')->orderBy('dateAdded', 'desc');
    }

    public function unseenRepliesMessages()
    {
        return $this->hasMany('App\UserMessage', 'replyTo')->where('isSeen', 0)->where('senderID', '!=', Auth::user()->userID)->orderBy('dateAdded', 'desc');
    }

    public function attachments()
    {
        return $this->hasMany('App\Model\Attachment', 'message_id');
    }
}
