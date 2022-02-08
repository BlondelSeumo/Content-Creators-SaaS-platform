<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CountryTax extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'tax_id',
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

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }

    public function tax()
    {
        return $this->belongsTo('App\Tax', 'tax_id');
    }
}
