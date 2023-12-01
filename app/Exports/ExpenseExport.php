<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class ExpenseExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Expense::get();
        
        foreach ($data as $k => $Expense) {
             
            $branch  = Expense::branchname($Expense->branch_id);
            $expensecategory  = Expense::expensecategory($Expense->category_id);
            
            unset($Expense->id,$Expense->created_by, $Expense->updated_at, $Expense->created_at);
            $data[$k]["branch_id"]          = $branch;
            $data[$k]["category_id"]          = $expensecategory;

        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "date",
            "branch_id",
            "category_id",
            "amount",
            "note",
        ];
    }
}
