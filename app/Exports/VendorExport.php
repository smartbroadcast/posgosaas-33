<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Vendor::get();

        foreach ($data as $k => $vendor) {
            unset($vendor->created_by,$vendor->updated_at, $vendor->created_at);
           
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Email",
            "phone_number",
            "address",
            "city",
            "state",
            "country",
            "zipcode",
            "is_active",
        ];
    }
}
