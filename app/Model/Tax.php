<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    public const INCLUSIVE_TYPE = 'inclusive';

    public const EXCLUSIVE_TYPE = 'exclusive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'percentage', 'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
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

    public function countries()
    {
        return $this->belongsToMany('App\Model\Country', 'country_taxes');
    }
}
