<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'invoice_id',
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
     * Virtual attributes
     */

    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get the transaction of this invoice.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'invoice_id');
    }
}
