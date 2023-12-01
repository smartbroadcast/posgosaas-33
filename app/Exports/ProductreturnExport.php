<?php

namespace App\Exports;

use App\Models\ProductsReturn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductreturnExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = ProductsReturn::get();

        foreach ($data as $k => $ProductsReturn) {

            $customer  = ProductsReturn::customers($ProductsReturn->customer_id);
            $venders  = ProductsReturn::vendors($ProductsReturn->vendor_id);
            unset($ProductsReturn->created_by, $ProductsReturn->updated_at, $ProductsReturn->created_at);
            $data[$k]["customer_id"]        = $customer;
            $data[$k]["vendor_id"]          = $venders;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "id",
            "date",
            "reference_no",
            "vendor_Name",
            "customer_Name",
            "return_note",
            "staff_note",
        ];
    }
}
