<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'created_by',
    ];

    public static function getProductBrandAnalysis(array $data)
    {
        
    	$authuser = Auth::user();

        $brands = Brand::where('created_by', $authuser->getCreatedBy());
        $purchased = Purchase::where('created_by', $authuser->getCreatedBy());
        $sold = Sale::where('created_by', $authuser->getCreatedBy());

        if ($data['brand_id'] != '-1')
        {
            $brands = $brands->where('id', $data['brand_id']);
        }

        if ($data['branch_id'] != '-1')
        {
            $purchased = $purchased->where('branch_id', $data['branch_id']);
            $sold = $sold->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $purchased = $purchased->where('cash_register_id', $data['cash_register_id']);
            $sold = $sold->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $purchased = $purchased->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
            $sold = $sold->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $purchased = $purchased->whereDate('created_at', '=', $date);
            $sold = $sold->whereDate('created_at', '=', $date);
        }

        $productbrand = [];

        $total_purchased_quantity = $total_sold_quantity = $total_purchased_price = $total_sold_price = $total_profit_or_loss = 0;
       
        if ($brands->count() > 0) {

            foreach($brands->get() as $counter => $brand) {

                $purchased_quantity = $sold_quantity = $purchased_price = $sold_price = 0;

                $product_ids = Product::where('brand_id', $brand->id)->pluck('id')->toArray();
             

                $purchasedCollection = clone $purchased;
                $purchasedCollection = $purchasedCollection->get();

                foreach ($purchasedCollection as $pc) {

                    $purchasedItemsArray = $pc->itemsArray();

                    foreach ($purchasedItemsArray['data'] as $itemvalue) {

                        if (in_array($itemvalue['id'], $product_ids)) {
                         
                            $purchased_quantity += $itemvalue['quantity'];
                            $purchased_price += $itemvalue['orgprice'];
                        }
                    }
                }

                $soldCollection = clone $sold;
                $soldCollection = $soldCollection->get();

                foreach ($soldCollection as $sc) {

                    $soldItemsArray = $sc->itemsArray();

                    foreach ($soldItemsArray['data'] as $itemvalue) {

                        if (in_array($itemvalue['id'], $product_ids)) {

                            $sold_quantity += $itemvalue['quantity'];
                            $sold_price += $itemvalue['orgprice'];
                        }
                    }
                }

                $total_purchased_quantity += $purchased_quantity;
                $total_sold_quantity += $sold_quantity;
                $total_purchased_price += $purchased_price;
                $total_sold_price += $sold_price;
                $total_profit_or_loss += $sold_price - $purchased_price;

                $profitlossdisplay = '<span class="badge p-2 px-3 rounded ' . (($sold_price - $purchased_price < 0) ? 'bg-danger' : 'bg-success') . '">' . number_format($sold_price - $purchased_price, 2) . '</span>';
              

                $productbrand[$counter]['id'] = $counter + 1;
                $productbrand[$counter]['name'] = $brand->name;
                $productbrand[$counter]['purchased_quantity'] = $purchased_quantity;
                $productbrand[$counter]['sold_quantity'] = $sold_quantity;
                $productbrand[$counter]['purchased_price'] = Auth::user()->priceFormat($purchased_price);
                $productbrand[$counter]['sold_price'] = Auth::user()->priceFormat($sold_price);
                $productbrand[$counter]['profitorloss'] = $profitlossdisplay;
            }
        }

        // dd($productbrand);
        $data['draw']            = 1;
        $data['recordsTotal']    = count($productbrand);
        $data['recordsFiltered'] = count($productbrand);
        $data['totalPurchasedQuantity'] = $total_purchased_quantity;
        $data['totalPurchasedPrice'] = Auth::user()->priceFormat($total_purchased_price);
        $data['totalSoldQuantity'] = $total_sold_quantity;
        $data['totalSoldPrice'] = Auth::user()->priceFormat($total_sold_price);
        $data['totalProfitOrLoss'] = '<span class="badge p-2 px-3 rounded ' . (($total_profit_or_loss < 0) ? 'bg-danger' : 'bg-success') . '">' . number_format($total_profit_or_loss, 2) . '</span>';
        $data['data']            = $productbrand;

        return $data;
    }
}
