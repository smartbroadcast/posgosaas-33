<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Expense Category'))
        {
            $expensecategories = ExpenseCategory::where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            return view('expensecategories.index')->with('expensecategories', $expensecategories);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Expense Category'))
        {
            return view('expensecategories.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Expense Category'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:expense_categories,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $expensecategory             = new ExpenseCategory();
            $expensecategory->name       = $request->name;
            $expensecategory->created_by = Auth::user()->getCreatedBy();
            $expensecategory->save();

            return redirect()->route('expensecategories.index')->with('success', __('Category added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(ExpenseCategory $expensecategory)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(ExpenseCategory $expensecategory)
    {
        if(Auth::user()->can('Edit Expense Category'))
        {
            return view('expensecategories.edit', compact('expensecategory'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, ExpenseCategory $expensecategory)
    {
        if(Auth::user()->can('Edit Expense Category'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:expense_categories,name,' . $expensecategory->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            $expensecategory->name = $request->name;
            $expensecategory->save();

            return redirect()->route('expensecategories.index')->with('success', __('Category updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(ExpenseCategory $expensecategory)
    {
        if(Auth::user()->can('Delete Expense Category'))
        {
            $expensecategory->delete();

            return redirect()->route('expensecategories.index')->with('success', __('Category deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
