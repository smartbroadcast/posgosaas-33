<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Customer extends Model
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

    public static function customer_id($customer_name)
    {
        $customers = DB::select(
            DB::raw("SELECT IFNULL( (SELECT id from customers where name = :name and created_by = :created_by limit 1), '0') as customer_id"), ['name' => $customer_name,  'created_by' => Auth::user()->getCreatedBy(), ]
        );

        return $customers[0]->customer_id;
    }

    public static function getCustomerSalesAnalysis(array $data)
    {
        $authuser = Auth::user();

        $customers = Customer::where('created_by', $authuser->getCreatedBy());
        $sold = Sale::where('created_by', $authuser->getCreatedBy());

        if ($data['customer_id'] != '-1')
        {
            $sold = $sold->where('customer_id', $data['customer_id']);
            $customers = $customers->where('id', $data['customer_id']);
        }

        if ($data['branch_id'] != '-1')
        {
            $sold = $sold->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $sold = $sold->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $sold = $sold->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $sold = $sold->whereDate('created_at', '=', $date);
        }

        $walk_in_customer_array = [];
        
        if ($data['customer_id'] == '-1' || $data['customer_id'] == '0')
        {
            $count_walk_in_customer = Sale::where('created_by', $authuser->getCreatedBy())->where('customer_id', 0)->count();
            
            if($count_walk_in_customer > 0) {

                $walk_in_customer_array = [
                                    '0' => [
                                            'id' => 0, 
                                            'name' => 'Walk-in Customers',
                                            'phone_number' => '',
                                            'email' => '',
                                          ]
                                    ];
            }
        }

        $customers = array_merge($walk_in_customer_array, $customers->get()->toArray());

        $productcustomer = [];

        $total_sold_quantity = $total_sold_price = 0;

        foreach($customers as $counter => $customer) {

            $sold_quantity = $sold_price = 0;

            $soldCollection = clone $sold;
            $soldCollection = $soldCollection->where('customer_id', $customer['id'])->get();

            foreach ($soldCollection as $sc) {

                $soldItemsArray = $sc->itemsArray();

                foreach ($soldItemsArray['data'] as $itemvalue) {

                    $sold_quantity += $itemvalue['quantity'];
                    $sold_price += $itemvalue['only_subtotal'];
                }
            }

            $total_sold_quantity += $sold_quantity;
            $total_sold_price += $sold_price;

            $productcustomer[$counter]['name'] = $customer['name'];
            $productcustomer[$counter]['phone_number'] = $customer['phone_number'];
            $productcustomer[$counter]['email_address'] = $customer['email'];
            $productcustomer[$counter]['total_sales'] = $sold_quantity;
            $productcustomer[$counter]['total_amount'] = Auth::user()->priceFormat($sold_price);
        }

        $data['draw']            = 1;
        $data['recordsTotal']    = count($productcustomer);
        $data['recordsFiltered'] = count($productcustomer);
        $data['totalSoldQuantity'] = $total_sold_quantity;
        $data['totalSoldPrice'] = Auth::user()->priceFormat($total_sold_price);
        $data['data']            = $productcustomer;

        return $data;
    }
}
