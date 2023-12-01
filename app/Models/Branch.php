<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'branch_type',
        'branch_manager',
        'tax',
        'created_by',
    ];
}
