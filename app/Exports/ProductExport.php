<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Product::get();
      
        foreach ($data as $k => $Product) {

            $taxe  = Product::tax($Product->tax_id);
            $unit  = Product::unit($Product->unit_id);
            $category  = Product::Category($Product->category_id);
            $brand  = Product::Brand($Product->brand_id);

            
            unset($Product->id,$Product->sku,$Product->image,$Product->created_by, $Product->updated_at, $Product->created_at);

            $data[$k]["id"]                = $Product->id;
            $data[$k]["tax_id"]        = $taxe;
            $data[$k]["unit_id"]          = $unit;
            $data[$k]["category_id"]   = $category;
            $data[$k]["brand_id"]   = $brand;


        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "name",
            "slug",
            "purchase_price",
            "sale_price",
            "description",
            "quantity",
            "tax_Name",
            "unit_Name",
            "category_Name",
            "brand_Name",
            "product_type",
        ];
    }
}
