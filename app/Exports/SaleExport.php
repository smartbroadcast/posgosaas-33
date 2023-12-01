<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class SaleExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Sale::get();
      
        foreach ($data as $k => $Sale) {

            $customer  = Sale::customers($Sale->customer_id);
            $branch  = Sale::branchname($Sale->branch_id);
            $cashregister  = Sale::cashregister($Sale->cash_register_id);
            if($Sale->status == 0){
                $status = 'Unpaid';
            }
            else if($Sale->status == 1)
            {
                $status = 'Partialy Paid';
            }
            else if($Sale->status == 2){
                $status = 'Paid';
            }
            unset($Sale->id,$Sale->created_by, $Sale->updated_at, $Sale->created_at);

            $data[$k]["id"]                = $Sale->id;
            $data[$k]["invoice_id"]        = Auth::user()->saleInvoiceNumberFormat($Sale->invoice_id);
            $data[$k]["customer_id"]        = $customer;
            $data[$k]["branch_id"]          = $branch;
            $data[$k]["cash_register_id"]   = $cashregister;
            $data[$k]["status"]              = $status;

        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "invoice_id",
            "customer_id",
            "branch_name",
            "cash_register_id",
            "status",
        ];
    }
}
