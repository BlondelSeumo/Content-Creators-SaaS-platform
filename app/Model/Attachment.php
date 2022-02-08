<?php

namespace App\Model;

use App\Providers\AttachmentServiceProvider;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public const PUBLIC_DRIVER = 0;
    public const S3_DRIVER = 1;
    public const WAS_DRIVER = 2;

    // Disable auto incrementing as we set the id manually (uuid)
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'post_id', 'filename', 'type', 'id', 'driver',
    ];

    protected $appends = ['attachmentType', 'path', 'thumbnail'];

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
     * Virtual attributes
     */

    public function getAttachmentTypeAttribute()
    {
        return AttachmentServiceProvider::getAttachmentType($this->type);
    }

    public function getPathAttribute()
    {
        return AttachmentServiceProvider::getFilePathByAttachment($this);
    }

    public function getThumbnailAttribute()
    {
        if ($this->message_id) {
            $path = '/messenger/images/';
        } else {
            $path = '/posts/images/';
        }

        return AttachmentServiceProvider::getThumbnailPathForAttachmentByResolution($this, 150, 150, $path);
    }

    /*
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function post()
    {
        return $this->belongsTo('App\Model\Post', 'post_id');
    }
}
