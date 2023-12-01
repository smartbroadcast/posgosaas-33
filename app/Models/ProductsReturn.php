<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsReturn extends Model
{
    protected $fillable = [
        'date',
        'reference_no',
        'vendor_id',
        'customer_id',
        'return_note',
        'staff_note',
        'created_by',
    ];

    public function vendor()
    {
        return $this->hasOne('App\Models\Vendor', 'id', 'vendor_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\ReturnedItems', 'return_id', 'id');
    }

    public function getTotal()
    {
        $subtotals = 0;
        foreach ($this->items as $item) {
            $subtotal = $item->price * $item->quantity;
            $tax      = ($subtotal * $item->tax) / 100;

            $subtotals += $subtotal + $tax;
        }

        return $subtotals;
    }
    public static function customers($customer)
    {
        
        $categoryArr  = explode(',', $customer);
        $unitRate = 0;
        foreach ($categoryArr as $customer) {
            if ($customer == 0) {
                $unitRate = '';
            } else {
                $customer        = Customer::find($customer);
                $unitRate        = !empty($customer->name) ? $customer->name :'';
            }
        }

        return $unitRate;
    }
    public static function vendors($venders)
    {
        
        $categoryArr  = explode(',', $venders);
        $unitRate = 0;
        foreach ($categoryArr as $venders) {
            if ($venders == 0) {
                $unitRate = '';
            } else {
                $venders        = Vendor::find($venders);
                $unitRate        = !empty($venders->name) ? $venders->name :'';
            }
        }

        return $unitRate;
    }
}
