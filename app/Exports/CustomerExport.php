<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Customer::get();

        foreach ($data as $k => $Customer) {
            unset($Customer->created_by,$Customer->updated_at, $Customer->created_at);

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
