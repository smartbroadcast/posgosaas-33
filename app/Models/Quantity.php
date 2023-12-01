<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quantity extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'branch_id',
        'cash_register_id',
        'user_id',
        'created_by',
    ];
}
