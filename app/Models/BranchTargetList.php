<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTargetList extends Model
{
    protected $fillable = [
        'target_id',
        'branch_id',
        'target_amount'
    ];

    public function branch()
    {
        return $this->hasOne('App\Models\Branch','id','branch_id');
    }
}
