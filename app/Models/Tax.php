<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'percentage',
        'is_default',
        'created_by',
    ];

    public static function getProductPurchaseTaxAnalysis(array $data)
    {
    	$authuser = Auth::user();

        $purchased = Purchase::where('created_by', $authuser->getCreatedBy());

        if ($data['vendor_id'] != '-1')
        {
            $purchased = $purchased->where('vendor_id', $data['vendor_id']);
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

        $productpurchasetax = [];

        $totalpurchasetax = $totalpurchasetaxamount = $totalpurchasesubtotal = 0;

        $purchasedCollection = $purchased->get();

        if ($purchasedCollection->count() > 0) {

	        foreach ($purchasedCollection as $counter => $pc) {

	        	$onepurchasetax = $onepurchasetaxamount = $onepurchasesubtotal = 0;

	        	$purchasedArray = $pc->toArray();

	            $purchasedArray['invoice_id'] = '<a href="#" data-ajax-popup="true" data-title="' . __('Purchase Invoice') . '" data-size="lg" data-url="' . route('show.purchase.invoice', $purchasedArray['id']) . '" class="btn btn-outline-primary">' . Auth::user()->purchaseInvoiceNumberFormat($purchasedArray['invoice_id']) . '</a>';
	            $purchasedArray['vendorname'] = $pc->vendor != null ? ucfirst($pc->vendor->name) : __('Walk-in Vendor');
	            $purchasedArray['created_at'] = Auth::user()->datetimeFormat($purchasedArray['created_at']);

	            $purchasedItemsArray = $pc->itemsArray();

	            foreach ($purchasedItemsArray['data'] as $itemvalue) {

	            	$onepurchasetax += $itemvalue['only_tax'];
	            	$onepurchasetaxamount += $itemvalue['only_tax_amount'];
	            	$onepurchasesubtotal += $itemvalue['only_subtotal'];
	            }

	        	$totalpurchasetax += $onepurchasetax;
	        	$totalpurchasetaxamount += $onepurchasetaxamount;
	        	$totalpurchasesubtotal += $onepurchasesubtotal;


				$purchasedArray['tax'] = $onepurchasetax . '%';
				$purchasedArray['tax_amount'] = Auth::user()->priceFormat($onepurchasetaxamount);
				$purchasedArray['sub_total'] = Auth::user()->priceFormat($onepurchasesubtotal);

	        	$productpurchasetax[$counter] = $purchasedArray;
	        }
	    }


        $data['draw']            = 1;
        $data['recordsTotal']    = count($productpurchasetax);
        $data['recordsFiltered'] = count($productpurchasetax);
        $data['totalPurchasedTax'] = $totalpurchasetax . '%';
        $data['totalPurchasedSubTotal'] = Auth::user()->priceFormat($totalpurchasesubtotal);
        $data['totalPurchasedTaxAmount'] = Auth::user()->priceFormat($totalpurchasetaxamount);
        $data['data']            = $productpurchasetax;

        return $data;
    }

    public static function getProductSaleTaxAnalysis(array $data)
    {
    	$authuser = Auth::user();

        $sale = Sale::where('created_by', $authuser->getCreatedBy());

        if ($data['customer_id'] != '-1')
        {
            $sale = $sale->where('customer_id', $data['customer_id']);
        }

        if ($data['branch_id'] != '-1')
        {
            $sale = $sale->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $sale = $sale->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $sale = $sale->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $sale = $sale->whereDate('created_at', '=', $date);
        }

        $productsaletax = [];

        $totalsaletax = $totalsaletaxamount = $totalsalesubtotal = 0;

        $saledCollection = $sale->get();

        if ($saledCollection->count() > 0) {

	        foreach ($saledCollection as $counter => $pc) {

	        	$onesaletax = $onesaletaxamount = $onesalesubtotal = 0;

	        	$saledArray = $pc->toArray();

	            $saledArray['invoice_id'] = '<a href="#" data-ajax-popup="true" data-title="' . __('Sale Invoice') . '" data-size="lg" data-url="' . route('show.sell.invoice', $saledArray['id']) . '" class="btn btn-outline-primary">' . Auth::user()->sellInvoiceNumberFormat($saledArray['invoice_id']) . '</a>';
	            $saledArray['customername'] = $pc->customer != null ? ucfirst($pc->customer->name) : __('Walk-in Customer');
	            $saledArray['created_at'] = Auth::user()->datetimeFormat($saledArray['created_at']);

	            $saledItemsArray = $pc->itemsArray();

	            foreach ($saledItemsArray['data'] as $itemvalue) {

	            	$onesaletax += $itemvalue['only_tax'];
	            	$onesaletaxamount += $itemvalue['only_tax_amount'];
	            	$onesalesubtotal += $itemvalue['only_subtotal'];
	            }

	        	$totalsaletax += $onesaletax;
	        	$totalsaletaxamount += $onesaletaxamount;
	        	$totalsalesubtotal += $onesalesubtotal;


				$saledArray['tax'] = $onesaletax . '%';
				$saledArray['tax_amount'] = Auth::user()->priceFormat($onesaletaxamount);
				$saledArray['sub_total'] = Auth::user()->priceFormat($onesalesubtotal);

	        	$productsaletax[$counter] = $saledArray;
	        }
        }


        $data['draw']            = 1;
        $data['recordsTotal']    = count($productsaletax);
        $data['recordsFiltered'] = count($productsaletax);
        $data['totalSaledTax'] = $totalsaletax . '%';
        $data['totalSaledSubTotal'] = Auth::user()->priceFormat($totalsalesubtotal);
        $data['totalSaledTaxAmount'] = Auth::user()->priceFormat($totalsaletaxamount);
        $data['data']            = $productsaletax;

        return $data;
    }
}
