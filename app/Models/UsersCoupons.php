<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersCoupons extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
    ];

    public function user_detail()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function coupon_detail()
    {
        return $this->hasOne('App\Models\Coupon', 'id', 'coupon_id');
    }
}
