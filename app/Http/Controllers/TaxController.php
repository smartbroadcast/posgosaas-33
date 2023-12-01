<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Tax;

class TaxController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Tax'))
        {
            $taxes = Tax::where('created_by', '=', Auth::user()->getCreatedBy())
                        ->orderBy('id', 'DESC')
                        ->get();

            return view('taxes.index')->with('taxes', $taxes);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Tax'))
        {
            return view('taxes.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Tax'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:40|unique:taxes,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                                   'percentage' => 'required|numeric|min:0|max:100||between:0,99.99',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            $tax             = new Tax();
            $tax->name       = $request->name;
            $tax->percentage = $request->percentage;
            $tax->is_default = $request->is_default;
            $tax->created_by = Auth::user()->getCreatedBy();
            $tax->save();

            return redirect()->route('taxes.index')->with('success', __('Tax added successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Tax $tax)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Tax $tax)
    {
        if(Auth::user()->can('Edit Tax'))
        {
            return view('taxes.edit', compact('tax'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Tax $tax)
    {
        if(Auth::user()->can('Edit Tax'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:40|unique:taxes,name,' . $tax->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                                //    'percentage' => 'required|numeric|check_percentage|min:0|max:100||between:0,99.99',
                                   'percentage' => 'required|numeric|min:0|max:100||between:0,99.99',

                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $tax->fill($request->all())->save();

            return redirect()->route('taxes.index')->with('success', __('Tax updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Tax $tax)
    {
        if(Auth::user()->can('Delete Tax'))
        {
            $tax->delete();

            return redirect()->route('taxes.index')->with('success', __('Tax deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
