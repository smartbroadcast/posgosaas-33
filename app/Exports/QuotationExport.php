<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuotationExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Quotation::get();

        foreach ($data as $k => $Quotation) {

            $customer  = Quotation::customers($Quotation->customer_id);
            if($Quotation->status == 0){
                $status = 'open';
            }
            else if($Quotation->status == 1){
                $status = 'close';
            }
            unset($Quotation->id,$Quotation->created_by,$Quotation->updated_at, $Quotation->created_at);
            $data[$k]["customer_id"]        = $customer;
            $data[$k]["status"]              = $status;
            
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "invoice_id",
            "date",
            "reference_no",
            "customer_id",
            "customer_email",
            "quotation_note",
            "status",
        ];
    }
}
