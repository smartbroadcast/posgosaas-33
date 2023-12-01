<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'parent_id',
        'type',
        'branch_id',
        'cash_register_id',
        'lang',
        'plan_expire_date',
        'plan_id',
        'is_active',
        'user_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];  

    // public function parent()
    // {
    //     return $this->hasOne('App\Models\User', 'id', 'parent_id');
    // }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function branch()
    {
        return $this->hasOne('App\Models\Branch','id','branch_id');
    }

    public function cashregister()
    {
        return $this->hasOne('App\Models\CashRegister','id','cash_register_id');
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }
    public function barcodeFormat()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_format'])?$settings['barcode_format']:'code128';
    }
    public function barcodeType()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_type'])?$settings['barcode_type']:'css';
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function datetimeFormat($datetime)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($datetime)) . ' ' . date($settings['site_time_format'], strtotime($datetime));
    }

    // public function purchaseInvoiceNumberFormat($number)
    // {  
        
    //     $settings = Utility::settings();

    //     return $settings["purchase_invoice_prefix"] . sprintf("%05d", $number);
    // }

    public static function purchaseInvoiceNumberFormat($number)
    {
        
        $settings = Utility::settings();

        return $settings["purchase_invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function saleInvoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["sale_invoice_prefix"] . sprintf("%05d", $number);
    }


    public function purchaseInvoiceColor()
    {
        $settings = Utility::settings();

        return $settings['purchase_invoice_color'];
    }

    public function sellInvoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["sale_invoice_prefix"] . sprintf("%05d", $number);
    }

    public function sellInvoiceColor()
    {
        $settings = Utility::settings();

        return $settings['sale_invoice_color'];
    }

    public function quotationInvoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["quotation_invoice_prefix"] . sprintf("%05d", $number);
    }

    public function quotationInvoiceColor()
    {
        $settings = Utility::settings();

        return $settings['quotation_invoice_color'];
    }

    public function getCreatedBy()
    {
        return ($this->parent_id == '0' || $this->parent_id == '1') ? $this->id : $this->parent_id;
    }

    public function creatorId()
    {
        return ($this->parent_id == '0' || $this->parent_id == '1') ? $this->id : $this->parent_id;
    }

    public function isSuperAdmin()
    {
        return $this->parent_id == 0 && $this->branch_id == 0 && $this->cash_register_id == 0;
    }

    public function isOwner()
    {

        return $this->parent_id != 0 && $this->branch_id == 0 && $this->cash_register_id == 0;
    }

    public function isUser()
    {
        return $this->parent_id != 0 && $this->parent_id != 1;
    }  

    public function isUsers()
    {
        return ($this->parent_id == '0' || $this->parent_id == '1');
    }

    public static function totalOwners()
    {
        return User::select('id', DB::raw('count(*) as count'))
            ->where('parent_id', '!=', '0')
            ->where('branch_id', '=', '0')
            ->where('cash_register_id', '=', '0')
            ->groupBy('id')
            ->get()
            ->count();
    }

    public static function countPaidOwners()
    {
        return User::where('parent_id', '!=', '0')
                   ->where('branch_id', '=', '0')
                   ->where('cash_register_id', '=', '0')
                   ->where('parent_id', '=', Auth::user()->id)
                   ->whereNotIn( 'plan_id', [ 0, 1 ] )
                   ->count();
    }

    public function assignPlan($plan_id)
    {
        $plan = Plan::find($plan_id);
        if($plan)
        {
            $this->plan_id = $plan->id;
            if($plan->duration == 'month')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            else if($plan->duration == 'year')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            else if($plan->duration == 'Unlimited')
            {
                $this->plan_expire_date = null;
            }
            $this->save();

            $users     = User::where('parent_id', '=', Auth::user()->getCreatedBy())->get();
            $customers = Customer::where('created_by', '=', Auth::user()->getCreatedBy())->get();
            $vendors   = Vendor::where('created_by', '=', Auth::user()->getCreatedBy())->get();

            $userCount = 0;
            foreach($users as $user)
            {
                $userCount++;
                $user->is_active = $plan->max_users == -1 || $userCount <= $plan->max_users  ? 1 : 0;
                $user->save();
            }

            $customerCount = 0;
            foreach($customers as $customer)
            {
                $customerCount++;
                $customer->is_active = $plan->max_customers == -1 ||$customerCount <= $plan->max_customers ? 1 : 0;
                $customer->save();
            }

            $vendorCount = 0;
            foreach($vendors as $vendor)
            {
                $vendorCount++;
                $vendor->is_active = $plan->max_vendors == -1 ||$vendorCount <= $plan->max_vendors ? 1 : 0;
                $vendor->save();
            }

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => __('Plan is deleted.'),
            ];
        }
    }
    public static function userDefaultDataRegister($user_id)
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();
    
        foreach ($allEmail as $email) {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => $user_id,
                    'is_active' => 1,
                ]
            );
        }
    }
    
    public static function getDefualtViewRouteByModule($module)
    {
        $userId      = \Auth::user()->id;
        // $defaultView = UserDefualtView::select('route')->where('module', $module)->where('user_id', $userId)->first();

        return !empty($defaultView) ? $defaultView->route : '';
    }

}
