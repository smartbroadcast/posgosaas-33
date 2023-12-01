<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'purchase_price',
        'sale_price',
        'description',
        'quantity',
        'tax_id',
        'unit_id',
        'category_id',
        'brand_id',
        'image',
        'product_type',
        'created_by',
    ];

    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id');
    }

    public static function getallproducts()
    {
        return Product::select('products.*', 'b.name as brandname', 'c.name as categoryname')
                    ->leftjoin('brands as b', 'b.id', '=', 'products.brand_id')
                    ->leftjoin('categories as c', 'c.id', '=', 'products.category_id')
                    ->where('products.created_by', '=', Auth::user()->getCreatedBy())
                    ->orderBy('products.id', 'DESC');
    }

    public static function tax_id($product_id)
    {
        $results = DB::select(
            DB::raw("SELECT IFNULL( (SELECT tax_id from products where id = :id and created_by = :created_by limit 1),  '0') as tax_id"), [ 'id' => $product_id,  'created_by' => Auth::user()->getCreatedBy(),]
        );
        return $results[0]->tax_id;
    }

    public function getTotalProductQuantity()
    {
        $totalquantity = $purchasedquantity = $selledquantity = 0;
       
        $authuser = Auth::user();
        $product_id = $this->id;

        $purchases = Purchase::where('created_by', $authuser->getCreatedBy());

        if ($authuser->isUser())
        {
            $purchases = $purchases->where('branch_id', $authuser->branch_id)->where('cash_register_id', $authuser->cash_register_id);
        }

        foreach($purchases->get() as $purchase)
        {
            $purchaseditem = PurchasedItems::select('quantity')->where('purchase_id', $purchase->id)->where('product_id', $product_id)->first();
            $purchasedquantity += $purchaseditem != null ? $purchaseditem->quantity : 0;
        }

        $sells = Sale::where('created_by', $authuser->getCreatedBy());

        if ($authuser->isUser())
        {
            $sells = $sells->where('branch_id', $authuser->branch_id)->where('cash_register_id', $authuser->cash_register_id);
        }

        foreach($sells->get() as $sell)
        {
            $selleditem = SelledItems::select('quantity')->where('sell_id', $sell->id)->where('product_id', $product_id)->first();
            $selledquantity += $selleditem != null ? $selleditem->quantity : 0;
        }

        $totalquantity = $purchasedquantity - $selledquantity;
        // dd($totalquantity);
        return $totalquantity;
    }

    public function getProductQuantityByBranch($data)
    {
        $totalquantity = $purchasedquantity = $selledquantity = 0;
        $authuser = Auth::user();
        $product_id = $this->id;

        $purchases = Purchase::where('created_by', $authuser->getCreatedBy());
        $sells = Sale::where('created_by', $authuser->getCreatedBy());

        if ($data['branch_id'] != '-1')
        {
            $purchases = $purchases->where('branch_id', $data['branch_id']);
            $sells = $sells->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $purchases = $purchases->where('cash_register_id', $data['cash_register_id']);
            $sells = $sells->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $purchases = $purchases->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
            $sells = $sells->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $purchases = $purchases->whereDate('created_at', '=', $date);
            $sells = $sells->whereDate('created_at', '=', $date);
        }

        foreach($purchases->get() as $purchase)
        {
            $purchaseditem = PurchasedItems::select('quantity')->where('purchase_id', $purchase->id)->where('product_id', $product_id)->first();
            $purchasedquantity += $purchaseditem != null ? $purchaseditem->quantity : 0;
        }

        foreach($sells->get() as $sell)
        {
            $selleditem = SelledItems::select('quantity')->where('sell_id', $sell->id)->where('product_id', $product_id)->first();
            $selledquantity += $selleditem != null ? $selleditem->quantity : 0;
        }

        $totalquantity = $purchasedquantity - $selledquantity;

        return $totalquantity;
    }

    public static function tax($taxe)
    {
        $categoryArr  = explode(',', $taxe);
        $unitRate = 0;
        foreach($categoryArr as $taxe)
        {
            $taxe    = Tax::find($taxe);
            $unitRate        = (!empty($taxe->name) ? $taxe->name : '');
            
        }

        return $unitRate;
    }

    public static function unit($unit)
    {
        $categoryArr  = explode(',', $unit);
        $unitRate = 0;
        foreach($categoryArr as $unit)
        {
            $unit    = Unit::find($unit);
            $unitRate        = (!empty($unit->name) ? $unit->name : '');
            
        }

        return $unitRate;
    }

    public static function Category($category)
    {
        $categoryArr  = explode(',', $category);
        $categoryRate = 0;
        foreach($categoryArr as $category)
        {
            $category    = Category::find($category);
            $categoryRate        = (!empty($category->name) ? $category->name :'');
            
        }

        return $categoryRate;
    }

    public static function Brand($brand)
    {
        $categoryArr  = explode(',', $brand);
        $brandRate = 0;
        foreach($categoryArr as $brand)
        {
            $brand    = Brand::find($brand);
            $brandRate        = (!empty($brand->name) ? $brand->name :'');
            
        }

        return $brandRate;
    }

}
