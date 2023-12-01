<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Role'))
        {
            $roles = Role::where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            $permissions = Permission::all();

            return view('roles.index', compact('roles', 'permissions'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Role'))
        {
            $permissions = Permission::all()->pluck('name', 'id')->toArray();

            return view('roles.create', ['permissions' => $permissions]);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Role'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|unique:roles,name,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $role             = new Role();
            $role->name       = $request['name'];
            $role->created_by = Auth::user()->getCreatedBy();
            $role->save();

            $permissions = $request['permissions'];

            if(isset($permissions) && !empty($permissions) && count($permissions) > 0)
            {
                foreach($permissions as $permission)
                {
                    $p = Permission::where('id', '=', $permission)->firstOrFail();
                    $role->givePermissionTo($p);
                }
            }

            return redirect()->route('roles.index')->with('success', $role->name . __(' Role added successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Role $role)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Role $role)
    {
        if(Auth::user()->can('Edit Role'))
        {
            $permissions = Permission::all()->pluck('name', 'id')->toArray();

            return view('roles.edit', compact('role', 'permissions'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Role $role)
    {
        if(Auth::user()->can('Edit Role'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'name' => 'required|unique:roles,name,' . $role->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $input       = $request->except( [ 'select-all', 'permissions' ] );
            $permissions = $request['permissions'];
            $role->fill($input)->save();

            $p_all = Permission::all();
            if(isset($p_all) && !empty($p_all) && count($p_all) > 0)
            {
                foreach($p_all as $p)
                {
                    $role->revokePermissionTo($p);
                }
            }

            if(isset($permissions) && !empty($permissions) && count($permissions) > 0)
            {
                foreach($permissions as $permission)
                {
                    $p = Permission::where('id', '=', $permission)->firstOrFail();
                    $role->givePermissionTo($p);
                }
            }

            return redirect()->route('roles.index')->with('success', $role->name . __(' Role updated successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Role $role)
    {
        if(Auth::user()->can('Delete Role'))
        {
            $role->delete();

            return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
