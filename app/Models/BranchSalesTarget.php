<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BranchSalesTarget extends Model
{
    protected $fillable = [
        'month',
        'created_by',
    ];

    public function targetlists()
    {
        return $this->hasMany('App\Models\BranchTargetList', 'target_id', 'id');
    }

    public static function getBranchTargets($month = false)
    {
        $saletarget = [];

        $targets = new BranchSalesTarget();

        $targets = $targets->where('created_by', '=', Auth::user()->getCreatedBy());
        if($month)
        {
            $targets = $targets->where('month', '=', [date('F-Y')]);
        }
        foreach($targets->orderBy('id', 'DESC')->get() as $key => $target)
        {
            $saletarget[$key]['id']    = $target->id;
            $saletarget[$key]['month'] = $target->month;

            foreach($target->targetlists as $targetlist)
            {
                $sells = Sale::where('created_by', '=', Auth::user()->getCreatedBy())
                             ->where('branch_id', '=', $targetlist->branch_id)
                             ->whereYear('created_at', '=', date('Y', strtotime($target->month)))
                             ->whereMonth('created_at', '=', date('m', strtotime($target->month)))
                             ->get();
                $total = 0;
                foreach($sells as $sell)
                {
                    $total += $sell->getTotal();
                }

                $totalprice  = $total;
                $targetprice = (int) $targetlist->target_amount;

                $percentage = $targetprice == 0 ? 0 : round(($totalprice / $targetprice) * 100);

                $per = $percentage <= 0 ? 0 : ($percentage <= 100 ? $percentage : ($percentage > 100 ? 100 : 0));

                if(Auth::user()->isOwner())
                {   
                    $saletarget[$key]['percentage'][]       = $per;
                    $saletarget[$key]['totalselledprice'][] = Auth::user()->priceFormat($totalprice);
                    $saletarget[$key]['branch'][]           = !empty($targetlist->branch->name) ? $targetlist->branch->name: '-';
                    $saletarget[$key]['totaltarget'][]      = Auth::user()->priceFormat($targetprice);
                }
                else if(Auth::user()->isUser() && Auth::user()->branch_id == $targetlist->branch->id)
                {
                    $saletarget[$key]['percentage'][]       = $per;
                    $saletarget[$key]['totalselledprice'][] = Auth::user()->priceFormat($totalprice);
                    $saletarget[$key]['branch'][]           = $targetlist->branch->name;
                    $saletarget[$key]['totaltarget'][]      = Auth::user()->priceFormat($targetprice);
                }
            }
        }

        uksort($saletarget, function ($a, $b) use ($saletarget) {

            $time1 = strtotime($saletarget[$a]['month']);
            $time2 = strtotime($saletarget[$b]['month']);

            return -strnatcasecmp($time1 , $time2);
        });

        return $saletarget;
    }
}
