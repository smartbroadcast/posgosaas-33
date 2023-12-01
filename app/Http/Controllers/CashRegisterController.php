<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\CashRegister;

class CashRegisterController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Cash Register'))
        {
            $cashregisters = CashRegister::select('cash_registers.*', 'branches.name as branchname')->leftjoin('branches', 'branches.id', '=', 'cash_registers.branch_id')->where('branches.created_by', '=', Auth::user()->getCreatedBy())->orderBy('cash_registers.id', 'DESC')->get();

            return view('cashregisters.index', compact('cashregisters'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Cash Register'))
        {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend(__('Choose Branch'), '');

            return view('cashregisters.create', compact('branches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Cash Register'))
        {
            if(!empty($request->input('branch_id')))
            {

                $validator = Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:100|unique:cash_registers,name,NULL,id,created_by,' . Auth::user()->getCreatedBy() . ',branch_id,' . $request->branch_id,
                                       'branch_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $cashregister = [
                    'name' => $request->input('name'),
                    'branch_id' => $request->input('branch_id'),
                    'created_by' => Auth::user()->getCreatedBy(),
                ];

                $cashregister = CashRegister::create($cashregister);

                return redirect()->route('cashregisters.index')->with('success', __('Cash Register added successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('The branch id field is required.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(CashRegister $cashRegister)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(CashRegister $cashregister)
    {
        if(Auth::user()->can('Edit Cash Register'))
        {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend('Choose Branch', '');

            return view('cashregisters.edit', compact('branches', 'cashregister'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, CashRegister $cashregister)
    {
        if(Auth::user()->can('Edit Cash Register'))
        {
            if(!empty($request->input('branch_id')))
            {
                $validator = Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:100|unique:cash_registers,name,' . $cashregister->id . ',id,created_by,' . Auth::user()->getCreatedBy() . ',branch_id,' . $request->branch_id,
                                       'branch_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $cashregister->name      = $request->name;
                $cashregister->branch_id = $request->branch_id;
                $cashregister->save();

                return redirect()->route('cashregisters.index')->with('success', __('Cash Register updated successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('The branch id field is required.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(CashRegister $cashregister)
    {
        if(Auth::user()->can('Delete Cash Register'))
        {
            $cashregister->delete();

            return redirect()->route('cashregisters.index')->with('success', __('Cash Register deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getCashRegisters(Request $request)
    {
        if(Auth::user()->can('Manage Cash Register') && $request->ajax())
        {
            $cashregisters = CashRegister::where('branch_id', '=', $request->branch_id)->get();

            return json_encode($cashregisters);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
