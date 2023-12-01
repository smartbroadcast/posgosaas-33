<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return redirect()->back();

        $permissions = Permission::orderBy('id', 'DESC')->get();

        return view('permissions.index')->with('permissions', $permissions);
    }

    public function create()
    {
        $roles = Role::where('created_by', '=', Auth::user()->getCreatedBy())->get();

        return view('permissions.create')->with('roles', $roles);
    }

    public function store(Request $request)
    {
        $permissions = $request['permissions'];
        $roles       = $request['roles'];

        if(isset($permissions) && !empty($permissions) && count($permissions) > 0)
        {
            foreach($permissions as $permission)
            {
                if(!empty($permission) && $permission !== '')
                {
                    $p = Permission::where('name', '=', $permission)->first();
                    if($p === null)
                    {
                        $newpermission       = new Permission();
                        $newpermission->name = $permission;
                        $newpermission->save();

                        if(!empty($roles))
                        {
                            foreach($roles as $role)
                            {
                                $r = Role::where('id', '=', $role)->firstOrFail();

                                $permissionto = Permission::where('name', '=', $newpermission->name)->first();
                                $r->givePermissionTo($permissionto);
                            }
                        }
                    }
                }
            }

            return redirect()->route('permissions.index')->with('success', __('Permissions added!'));
        }

        return redirect()->route('permissions.index')->with('error', __('Enter Permissions !'));
    }

    public function show(Permission $permission)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:200|unique:permissions,name,' . $permission->id,
                           ]
        );

        if($validator->fails())
        {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('permissions.index')->with('success', __('Permission : ') . $permission->name . __(' updated!'));
    }

    public function destroy(Permission $permission)
    {
        if($permission->name == "Delete Permission")
        {
            return redirect()->route('permissions.index')->with('success', __('You Cannot delete this Permission!'));
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', __('Permission deleted successfully!'));

    }
}
