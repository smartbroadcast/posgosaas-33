<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpenseExport;

class ExpenseController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Expense')) {
            $expenses = Expense::select('expenses.*', 'branches.name as branchname', 'expense_categories.name as ecname')
                ->leftjoin('branches', 'branches.id', '=', 'expenses.branch_id')
                ->leftjoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
                ->where('expenses.created_by', '=', Auth::user()->getCreatedBy())
                ->orderBy('expenses.id', 'DESC')
                ->get();

            return view('expenses.index', compact('expenses'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('Create Expense')) {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend(__('Choose Branch'), '');

            $expensecategories = ExpenseCategory::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $expensecategories->prepend(__('Choose Category'), '');

            return view('expenses.create', compact('branches', 'expensecategories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('Create Expense')) {
            if (!empty($request->input('branch_id')) && !empty($request->input('category_id'))) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'date' => 'required|date',
                        'branch_id' => 'required',
                        'category_id' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $expense              = new Expense();
                $expense->date        = date('Y-m-d', strtotime($request->date));
                $expense->branch_id   = $request->branch_id;
                $expense->category_id = $request->category_id;
                $expense->amount      = (int)$request->amount;
                $expense->note        = $request->note;
                $expense->created_by  = Auth::user()->getCreatedBy();
                $expense->save();

                return redirect()->route('expenses.index')->with('success', __('Expense added successfully.'));
            } else {
                return redirect()->back()->with('error', __('The branch/category field is required.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Expense $expense)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Expense $expense)
    {
        if (Auth::user()->can('Edit Expense')) {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend(__('Choose Branch'), '');

            $expensecategories = ExpenseCategory::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $expensecategories->prepend(__('Choose Category'), '');

            return view('expenses.edit', compact('expense', 'branches', 'expensecategories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Expense $expense)
    {
        if (Auth::user()->can('Edit Expense')) {
            if (!empty($request->input('branch_id')) && !empty($request->input('category_id'))) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'date' => 'required|date',
                        'branch_id' => 'required',
                        'category_id' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $expense->date        = date('Y-m-d', strtotime($request->date));
                $expense->branch_id   = $request->branch_id;
                $expense->category_id = $request->category_id;
                $expense->amount      = (int)$request->amount;
                $expense->note        = $request->note;
                $expense->created_by  = Auth::user()->getCreatedBy();
                $expense->save();

                return redirect()->route('expenses.index')->with('success', __('Expense updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The branch/category field is required.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Expense $expense)
    {
        if (Auth::user()->can('Delete Expense')) {
            $expense->delete();

            return redirect()->route('expenses.index')->with('success', __('Expense deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function export()
    {
        $name = 'Expense_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ExpenseExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }
}
