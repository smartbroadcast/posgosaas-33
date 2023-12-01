<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'country',
        'zipcode',
        'is_active',
        'created_by',
    ];

    public static function vendor_id($vendor_name)
    {
        $vendors = DB::select(
            DB::raw("SELECT IFNULL( (SELECT id from vendors where name = :name and created_by = :created_by limit 1), '0') as vendor_id"), ['name' => $vendor_name, 'created_by' => Auth::user()->getCreatedBy(),]
        );

        return $vendors[0]->vendor_id;
    }

    public static function getVendorPurchasedAnalysis(array $data)
    {
        $authuser = Auth::user();

        $vendors = Vendor::where('created_by', $authuser->getCreatedBy());
        $purchased = Purchase::where('created_by', $authuser->getCreatedBy());

        if ($data['vendor_id'] != '-1')
        {
            $purchased = $purchased->where('vendor_id', $data['vendor_id']);
            $vendors = $vendors->where('id', $data['vendor_id']);
        }

        if ($data['branch_id'] != '-1')
        {
            $purchased = $purchased->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $purchased = $purchased->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $purchased = $purchased->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $purchased = $purchased->whereDate('created_at', '=', $date);
        }

        $walk_in_vendor_array = [];
        
        if ($data['vendor_id'] == '-1' || $data['vendor_id'] == '0')
        {
            $count_walk_in_vendor = Purchase::where('created_by', $authuser->getCreatedBy())->where('vendor_id', 0)->count();
            
            if($count_walk_in_vendor > 0) {

                $walk_in_vendor_array = [
                                    '0' => [
                                            'id' => 0, 
                                            'name' => 'Walk-in Vendors',
                                            'phone_number' => '',
                                            'email' => '',
                                          ]
                                    ];
            }
        }

        $vendors = array_merge($walk_in_vendor_array, $vendors->get()->toArray());

        $productvendor = [];

        $total_purchased_quantity = $total_purchased_price = 0;

        foreach($vendors as $counter => $vendor) {

            $purchased_quantity = $purchased_price = 0;

            $purchasedCollection = clone $purchased;
            $purchasedCollection = $purchasedCollection->where('vendor_id', $vendor['id'])->get();

            foreach ($purchasedCollection as $sc) {

                $purchasedItemsArray = $sc->itemsArray();

                foreach ($purchasedItemsArray['data'] as $itemvalue) {

                    $purchased_quantity += $itemvalue['quantity'];
                    $purchased_price += $itemvalue['only_subtotal'];
                }
            }

            $total_purchased_quantity += $purchased_quantity;
            $total_purchased_price += $purchased_price;

            $productvendor[$counter]['name'] = $vendor['name'];
            $productvendor[$counter]['phone_number'] = $vendor['phone_number'];
            $productvendor[$counter]['email_address'] = $vendor['email'];
            $productvendor[$counter]['total_sales'] = $purchased_quantity;
            $productvendor[$counter]['total_amount'] = Auth::user()->priceFormat($purchased_price);
        }

        $data['draw']            = 1;
        $data['recordsTotal']    = count($productvendor);
        $data['recordsFiltered'] = count($productvendor);
        $data['totalPurchasedQuantity'] = $total_purchased_quantity;
        $data['totalPurchasedPrice'] = Auth::user()->priceFormat($total_purchased_price);
        $data['data']            = $productvendor;

        return $data;
    }
}
