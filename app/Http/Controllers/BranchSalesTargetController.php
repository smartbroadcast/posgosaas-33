<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;
use App\Models\BranchSalesTarget;
use App\Models\BranchTargetList;

class BranchSalesTargetController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Branch Sales Target'))
        {   
            $saletarget = BranchSalesTarget::getBranchTargets();
            

            return view('branchsalestargets.index', compact('saletarget'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Branch Sales Target'))
        {
            $branches = Branch::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');

            return view('branchsalestargets.create', compact('branches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Branch Sales Target'))
        {
            $branches = Branch::where('created_by', Auth::user()->getCreatedBy())->count();

            if ($branches == 0)
            {
                return redirect()->back()->with('error', __("Please add some Branches!"));
            }
            
            $validator = Validator::make(
                $request->all(), [
                                    'month' => 'required|unique:branch_sales_targets,month,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                                ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $bst             = new BranchSalesTarget();
            $bst->month      = $request->month;
            $bst->created_by = Auth::user()->getCreatedBy();
            $bst->save();

            if($request->has('branches') && $request->has('amount'))
            {
                $branches = $request->branches;
                $amountes = $request->amount;

                if(count($branches) == count($amountes))
                {
                    for($i = 0; $i < count($branches); $i++)
                    {
                        $branch_id = $branches[$i];
                        $amount    = $amountes[$i];

                        $btl                = new BranchTargetList();
                        $btl->target_id     = $bst->id;
                        $btl->branch_id     = $branch_id;
                        $btl->target_amount = $amount;
                        $btl->save();
                    }
                }
            }

            return redirect()->route('branchsalestargets.index')->with('success', __('Sales Target created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(BranchSalesTarget $branchsalestarget)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(BranchSalesTarget $branchsalestarget)
    {
        if(Auth::user()->can('Edit Branch Sales Target'))
        {
            $targetedbranches = BranchTargetList::select('branch_target_lists.branch_id', 'branches.name', 'branch_target_lists.target_amount')->join('branches', 'branches.id', '=', 'branch_target_lists.branch_id')->where('branch_target_lists.target_id', '=', $branchsalestarget->id)->get();

            return view('branchsalestargets.edit', compact('branchsalestarget', 'targetedbranches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, BranchSalesTarget $branchsalestarget)
    {
        if(Auth::user()->can('Edit Branch Sales Target'))
        {
            if($request->has('branches') && $request->has('amount'))
            {
                $branches = $request->branches;
                $amountes = $request->amount;

                if(count($branches) == count($amountes))
                {
                    for($i = 0; $i < count($branches); $i++)
                    {
                        $branch_id = $branches[$i];
                        $amount    = $amountes[$i];

                        BranchTargetList::where('branch_id', $branch_id)
                                        ->where('target_id', $branchsalestarget->id)
                                        ->update(['target_amount' => $amount]);
                    }
                }
            }

            return redirect()->route('branchsalestargets.index')->with('success', __('Sales Target updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(BranchSalesTarget $branchsalestarget)
    {
        if(Auth::user()->can('Delete Branch Sales Target'))
        {
            BranchTargetList::where('target_id', $branchsalestarget->id)->delete();
            $branchsalestarget->delete();

            return redirect()->route('branchsalestargets.index')->with('success', __('Sales Target deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
