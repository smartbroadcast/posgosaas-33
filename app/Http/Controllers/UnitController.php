<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Unit'))
        {
            $units = Unit::where('created_by', '=', Auth::user()->getCreatedBy())
                         ->orderBy('id', 'DESC')
                         ->get();

            return view('units.index')->with('units', $units);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Unit'))
        {
            return view('units.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Unit'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:50|unique:units,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                                   'shortname' => 'required|max:50',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $unit             = new Unit();
            $unit->name       = $request->name;
            $unit->shortname  = $request->shortname;
            $unit->created_by = Auth::user()->getCreatedBy();
            $unit->save();

            return redirect()->route('units.index')->with('success', __('Unit added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Unit $unit)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Unit $unit)
    {
        if(Auth::user()->can('Edit Unit'))
        {
            return view('units.edit', compact('unit')); //pass user and roles data to view
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Unit $unit)
    {
        if(Auth::user()->can('Edit Unit'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:50|unique:units,name,' . $unit->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                                   'shortname' => 'required|max:50',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $unit->fill($request->all())->save();

            return redirect()->route('units.index')->with('success', __('Unit updated successfully.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Unit $unit)
    {
        if(Auth::user()->can('Delete Unit'))
        {
            $unit->delete();

            return redirect()->route('units.index')->with('success', __('Unit deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
