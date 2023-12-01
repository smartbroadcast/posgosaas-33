<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'from',
        'to',
        'color',
        'description',
        'created_by',
    ];

    public static function getAllNotifications()
    {
        $today = date('Y-m-d');

        Notification::where('created_by', '=', Auth::user()->getCreatedBy())->whereDate('to', '<', $today)->update(['status' => '1']);

        $notifications = Notification::select('color', 'description')->where('from', '<=', $today)->where('to', '>=', $today)->where('created_by', Auth::user()->getCreatedBy())->where('status', '=', '0')->get();

        return $notifications;
    }
}
