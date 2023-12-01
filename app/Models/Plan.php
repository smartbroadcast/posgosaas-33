<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_users',
        'max_customers',
        'max_vendors',
        'description',
        // 'image',
    ];

    public static $arrDuration = [
        'Unlimited' => 'Unlimited',
        'month' => 'Per Month',
        'year' => 'Per Year',
    ];

     public function status()
    {
        return [
            __('Unlimited'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function totalPlan()
    {
        return Plan::count();
    }

    public static function most_purchased_plan()
    {
        $plan =  User::select('users.plan_id', 'plans.name as planname', DB::raw('count(plan_id) as plans_occurrence'))
                   ->join('plans', 'plans.id' ,'=', 'users.plan_id')
                   ->where('users.parent_id', '!=', '0')
                   ->where('users.branch_id', '=', '0')
                   ->where('users.cash_register_id', '=', '0')
                   ->where('users.parent_id', '=', Auth::user()->id)
                   ->whereNotIn( 'users.plan_id', [ 0, 1 ] )
                   ->orderBy('plans_occurrence', 'DESC')
                   ->groupBy('users.plan_id')
                   ->first();

       return ($plan != null) ? $plan->planname : '';
    }
}
