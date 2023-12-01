<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Brand'))
        {
            $brands = Brand::where('created_by', '=', Auth::user()->getCreatedBy())
                                ->orderBy('id', 'DESC')
                                ->get();

            return view('brands.index')->with('brands', $brands);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Brand'))
        {
            return view('brands.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Brand'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:brands,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $brand             = new Brand();
            $brand->name       = $request->name;
            $brand->slug       = Str::slug($request->name, '-');
            $brand->created_by = Auth::user()->getCreatedBy();
            $brand->save();

            return redirect()->route('brands.index')->with('success', __('Brand added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Brand $brand)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Brand $brand)
    {
        if(Auth::user()->can('Edit Brand'))
        {
            return view('brands.edit', compact('brand'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Brand $brand)
    {
        if(Auth::user()->can('Edit Brand'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:brands,name,' . $brand->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->name, '-');
            $brand->save();

            return redirect()->route('brands.index')->with('success', __('Brand updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Brand $brand)
    {
        if(Auth::user()->can('Delete Brand'))
        {
            $brand->delete();

            return redirect()->route('brands.index')->with('success', __('Brand deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
