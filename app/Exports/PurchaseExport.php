<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class PurchaseExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Purchase::get();
        
        foreach ($data as $k => $Purchase) {
             
            $venders  = Purchase::vendors($Purchase->vendor_id);
            $branch  = Purchase::branchname($Purchase->branch_id);
            $cashregister  = Purchase::cashregister($Purchase->cash_register_id);
            if($Purchase->status == 0){
              $status = 'Unpaid';
            }
            else if($Purchase->status == 1)
            {
                $status = 'Partial paid';
            }
            else if($Purchase->status == 2)
            {
                $status = 'paid';
            }

            unset($Purchase->id,$Purchase->created_by, $Purchase->updated_at, $Purchase->created_at);
            $data[$k]["invoice_id"]         = Auth::user()->purchaseInvoiceNumberFormat($Purchase->invoice_id);
            $data[$k]["vendor_id"]          = $venders;
            $data[$k]["branch_id"]          = $branch;
            $data[$k]["cash_register_id"]   = $cashregister;
            $data[$k]["status"]   = $status;



        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "invoice_id",
            "vendor_id",
            "branch_id",
            "cash_register_id",
            "status",
        ];
    }
}
