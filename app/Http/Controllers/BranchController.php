<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\User;

class BranchController extends Controller
{
    public function index()
    {  
        if(Auth::user()->can('Manage Branch'))
        {
            $branches = DB::table('branches as b')->leftjoin('cash_registers as cr', 'cr.branch_id', '=', 'b.id')->leftjoin('users as u', 'u.id', '=', 'b.branch_manager')->select('b.*', 'u.name as username', DB::raw("count(cr.branch_id) branchtotal"))->where('b.created_by', '=', Auth::user()->getCreatedBy())->groupBy('b.id')->orderBy('b.id', 'DESC')->get();

            return view('branches.index')->with('branches', $branches);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Branch'))
        {
            $users = User::where('parent_id', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $users->prepend('Choose User', '');

            return view('branches.create', compact('users')); 
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Branch'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|unique:branches,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $branch['name']        = $request->input('name');
            $branch['branch_type'] = $request->input('branch_type');
            if(!empty($request->input('branch_manager')))
            {
                $branch['branch_manager'] = $request->branch_manager;
            }
            $branch['created_by'] = Auth::user()->getCreatedBy();

            Branch::create($branch);

            return redirect()->route('branches.index')->with('success', __('Branch added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Branch $branch)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Branch $branch)
    {
        if(Auth::user()->can('Edit Branch'))
        {
            $users = User::where('parent_id', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $users->prepend(__('Choose User'), '');

            return view('branches.edit', compact('branch', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Branch $branch)
    {
        if(Auth::user()->can('Edit Branch'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|unique:branches,name,' . $branch->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $branch->name        = $request->name;
            $branch->branch_type = $request->branch_type;
            if(!empty($request->input('branch_manager')))
            {
                $branch->branch_manager = $request->branch_manager;
            }
            $branch->save();

            return redirect()->route('branches.index')->with('success', __('Branch updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Branch $branch)
    {
        if(Auth::user()->can('Delete Branch'))
        {
            $branch->delete();

            return redirect()->route('branches.index')->with('success', __('Branch deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getBranches(Request $request, Branch $branch)
    {
        if(Auth::user()->can('Manage Branch') && $request->ajax())
        {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->get();

            return json_encode($branches);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
