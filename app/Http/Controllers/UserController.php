<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Mail\UserCreate;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Plan;
use App\Models\Order;
use App\Models\User;
use App\Models\Utility;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage User')) {
            $users = User::select('users.*', DB::raw("COUNT(cu.parent_id) users"))->leftjoin('users as cu', 'cu.parent_id', '=', 'users.id')->where('users.parent_id', '=', Auth::user()->getCreatedBy())->groupBy('users.id')->orderBy('users.id', 'DESC')->get();
            
            return view('users.index')->with('users', $users);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('Create User')) {
            $roles = Role::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $roles->prepend(__('Assign Role'), '');

            $branches = Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend(__('Select Branch'), '');

            return view('users.create', compact('roles', 'branches'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {     

        // dd($request->all());
        if (Auth::user()->can('Create User')) {
            $validatorArray = [
                'name' => 'required|max:120',
                'email' => 'required|email|max:100|unique:users,email,NULL',
                //    'email' => 'required|email|max:100|unique:users,email,NULL,id,parent_id,' . Auth::user()->getCreatedBy(),
                'password' => 'required|min:4|confirmed',
            ];

            if (Auth::user()->isOwner() || Auth::user()->isUser()) {
                $validatorArray['branch_id'] = 'required';
                $validatorArray['cash_register_id'] = 'required';
            }

            $validator = Validator::make(
                $request->all(),
                $validatorArray
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            if (!empty(env('DEFAULT_LANG'))) {
                $default_language = env('DEFAULT_LANG');
            } else {
                $default_language = 'en';
            }

            $owneruser = User::where('id', '=', \Auth::user()->getCreatedBy())->first();
            $total_user = User::where('parent_id', '=', $owneruser->getCreatedBy())->count();

            $plan = Plan::find($owneruser->plan_id);

            if (\Auth::user()->isSuperAdmin() || $plan->max_users == -1 || $total_user < $plan->max_users) {
                $userpassword      = $request->input('password');
                $user['name']      = $request->input('name');
                $user['email']     = $request->input('email');
                $user['address']   = $request->input('address');
              
                $user['password']  = $userpassword;
                $user['parent_id'] = $owneruser->getCreatedBy();

                if (\Auth::user()->isSuperAdmin()) {
                    $user['plan_id']   = 1;
                }

                if (!empty($request->input('branch_id'))) {
                    $user['branch_id'] = $request->input('branch_id');
                }
                if (!empty($request->input('cash_register_id'))) {
                    $user['cash_register_id'] = $request->input('cash_register_id');
                }
                $user['lang']        = $default_language;
                $user['is_active']   = 1;
                $user['user_status'] = 1;

                if (Auth::user()->isOwner() || Auth::user()->isUser()) {
                        $role = Role::find($request['role']);
                        $user['type']   = $role->name;
                }
        // dd($user);
                if (\Auth::user()->isSuperAdmin()){
                        $role = Role::find($request['role']);
                        $user['type']   = 'Owner';
                }

                $user = User::create($user);

                $role = $request['role'];


                if ($owneruser->parent_id == 0) {
                    $role_r = Role::where('id', '=', 2)->firstOrFail();
                    $user->assignRole($role_r);
                } else if (isset($role) && !empty($role) && $user->parent_id != 0) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $user->assignRole($role_r);
                }
 

                // dd($role_r);

                try {
                    $user->type = Auth::user()->isSuperAdmin() ? 'Owner' : 'User';

                    if($role_r->name == 'Owner'){  
                        
                        $uArr = [   
                            'app_name'  =>env('APP_NAME'),
                            'app_url'=> env('APP_URL'),
                            'owner_name' => $request->name,
                            'owner_email' => $request->email,
                            'owner_password' => $userpassword,
                            
                          ];

                          $resp = Utility::sendEmailTemplate('new_owner', [$user->id => $user->email], $uArr);
                    }
                    
                    if($role_r->name == 'Employee'){ 

                        $uArr = [   
                            'app_name'  =>env('APP_NAME'),
                            'app_url'=> env('APP_URL'),
                            'user_name' => $request->name,
                            'user_email' => $request->email,
                            'user_password' => $userpassword,
                            
                          ];
                          $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);
                    }


                    $user->userpassword = $userpassword;    
                    $user->userDefaultDataRegister($user->id);
                    

                    // Mail::to($user->email)->send(new UserCreate($user));
                } catch (\Exception $e) { 
  
                    
                    $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
                }

                return redirect()->route('users.index')->with('success', __('User added successfully.') . ((isset($smtp_error)) ? $smtp_error : ''));
            } else {
                return redirect()->back()->with('error', __('Your user limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(User $user)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(User $user)
    {
        if (Auth::user()->can('Edit User')) {
            $roles = Role::where('created_by', '=', Auth::user()->getCreatedBy())->pluck('name', 'id');

            $branches = Branch::where('created_by', Auth::user()->getCreatedBy())->pluck('name', 'id');
            $branches->prepend(__('Select Branch'), '');

            $cash_registers = CashRegister::where('branch_id', $user->branch_id)->pluck('name', 'id');
            $cash_registers->prepend(__('Select Cash Register'), '');

            return view('users.edit', compact('user', 'roles', 'branches', 'cash_registers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->can('Edit User')) {
            $validatorArray = [
                'name' => 'required|max:120',
                'email' => 'required|email|max:100|unique:users,email,' . $user->id . ',id,parent_id,' . Auth::user()->getCreatedBy(),
                'password' => 'required|min:4|confirmed',
            ];

            if (Auth::user()->isOwner() || Auth::user()->isUser()) {
                $validatorArray['branch_id'] = 'required';
                $validatorArray['cash_register_id'] = 'required';
            }

            $validator = Validator::make(
                $request->all(),
                $validatorArray
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->address  = $request->address;
            $user->password = $request->password;
            if (!empty($request->input('branch_id'))) {
                $user->branch_id = $request->branch_id;
            }
            if (!empty($request->input('cash_register_id'))) {
                $user->cash_register_id = $request->cash_register_id;
            }
            $user->save();

            if ($user->parent_id != 0) {
                $roles = $request['roles'];

                if (isset($roles)) {
                    $user->roles()->sync($roles);
                } else {
                    $user->roles()->detach();
                }
            }

            return redirect()->route('users.index')->with('success', __('User successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function changeUserStatus($id)
    {
        if (Auth::user()->can('Manage User')) {
            $user   = User::find($id);
            $status = '';
            if ($user) {
                User::where('id', $id)->update(['user_status' => (int)!$user->user_status]);
                User::where('parent_id', $id)->update(['user_status' => (int)!$user->user_status]);
                $status = $user->user_status == '0' ? __('activated') : __('deactivated');
            }

            return redirect()->route('users.index')->with('success', __('User') . ' ' . $status . ' ' . __('successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(User $user)
    {
        if (Auth::user()->can('Delete User')) {
            return redirect()->route('users.index')->with('success', __('User successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function userPassword($id)
    {
        $eId        = \Crypt::decrypt($id);

        $user = User::find($eId);

        $employee = User::where('id', $eId)->first();



        return view('users.reset', compact('user', 'employee'));
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',

            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user = User::where('id', $id)->first();
        $user->password = $request->password;
        $user->save();


        return redirect()->route('users.index')->with(
            'success',
            'Users Password successfully updated.'
        );
    }


    public function displayProfile(Request $request)
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    // public function uploadProfile(Request $request)
    // {
    //     $user      = Auth::user();
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'name' => [
    //                 'bail',
    //                 'required',
    //                 'string',
    //                 'min:2',
    //                 'max:255',
    //             ],
    //             'email' => 'required|email|unique:users,email,' . $user->id,
    //             'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         return redirect()->back()->with('error', $validator->errors()->first());
    //     }

    //     if ($request->hasFile('avatar')) {
    //         $validator = Validator::make(
    //             $request->all(),
    //             [
    //                 'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
    //             ]
    //         );

    //         if ($validator->fails()) {
    //             return redirect()->back()->with('error', $validator->errors()->first());
    //         }

          
    //         $filenameWithExt = $request->file('avatar')->getClientOriginalName();
    //         $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    //         $extension       = $request->file('avatar')->getClientOriginalExtension();
    //         $fileNameToStore = $filename . '_' . time() . '.' . $extension;
    //         // $path            = $request->file('avatar')->storeAs('avatar', $fileNameToStore);
    //         // $user['avatar']  = $path;
    //         $settings = Utility::getStorageSetting();   
    //         $dir        = 'uploads/avatar/';

            

    //         $path = Utility::upload_file($request,'avatar',$filenameWithExt,$dir,[]);
            
    //         if($path['flag'] == 1){
    //             $url = $path['url'];
    //         }else{
    //             return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
    //         }
    //     }

    //     $user['name'] = $request['name'];
    //     $user['email'] = $request['email'];
    //     $user->save();

    //     return redirect()->route('profile.display')->with('success', __('Profile updated successfully.'));
    // }

    public function uploadProfile(Request $request)
    {

        $userDetail = \Auth::user();
        $user       = User::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                    ]
        );
        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $settings = Utility::getStorageSetting();

            if($settings['storage_setting']=='local'){
                $dir        = 'uploads/avatar/';
            }
            else{
                    $dir        = 'uploads/avatar';
                }
            $image_path = $dir . $userDetail['avatar'];

            if(\File::exists($image_path))
            {
                File::delete($image_path);
            }

                // if(!file_exists($dir))
                // {
                //     mkdir($dir, 0777, true);
                // }
            $url = '';
            // $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);
            // dd($path);
            $path = Utility::upload_file($request,'profile',$filenameWithExt,$dir,[]);
            
            if($path['flag'] == 1){
                $url = $path['url'];
            }else{
                return redirect()->route('profile.display', \Auth::user()->id)->with('error', __($path['msg']));
            }

        // dd($path);
            // $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] =  $url;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();
        // CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }
    public function deleteProfile(Request $request)
    {
        $user = Auth::user();
        if (asset(Storage::exists($user->avatar))) {
            asset(Storage::delete($user->avatar));
        }
        $user->avatar = '';
        $user->save();

        return redirect()->route('profile.display')->with('success', __('Profile deleted successfully.'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required',
                'password' => 'required|same:password',
                'confirm_password' => 'required|same:password',
            ]
        );
        $objUser          = Auth::user();
        $request_data     = $request->all();
        $current_password = $objUser->password;

        if (Hash::check($request_data['current_password'], $current_password)) {
            $objUser->password = $request_data['password'];
            $objUser->save();

            return redirect()->route('profile.display')->with('success', __('Password updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Please Enter Correct Current Password!'));
        }
    }

    public function checkUserType()
    {


        $user = User::select('id')->where('id', '=', Auth::user()->getCreatedBy())->where('user_status', '=', 1)->get();

        if (!empty($user) && count($user) > 0) {
            $user[0]['isSuperAdmin'] = Auth::user()->isSuperAdmin();
            $user[0]['isOwner']      = Auth::user()->isOwner();
            $user[0]['isUser']       = Auth::user()->isUser();
            if (Auth::user()->isUser()) {
                $user[0]['branch_id']        = Auth::user()->branch->id;
                $user[0]['branchname']       = Auth::user()->branch->name;
                $user[0]['cash_register_id'] = Auth::user()->cashregister->id;
                $user[0]['cashregistername'] = Auth::user()->cashregister->name;
            }
        }

        return json_encode($user);
    }

    public function upgradePlan($user_id)
    {
        $user  = User::find($user_id);
        $plans = Plan::get();

        return view('users.plan', compact('user', 'plans'));
    }

    public function activePlan($user_id, $plan_id)
    {
        $user          = User::find($user_id);
        $user->plan_id = $plan_id;
        $user->save();

        $plan       = Plan::find($plan_id);
        $assignPlan = $user->assignPlan($plan_id);

        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $order_id,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    "price_currency" => env('CURRENCY') != '' ? env('CURRENCY') : '',
                    'txn_id' => '',
                    'payment_type' => __('Manually Upgrade By Super Admin'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );
        }

        return redirect()->back()->with('success', 'Plan successfully activated.');
    }
}
