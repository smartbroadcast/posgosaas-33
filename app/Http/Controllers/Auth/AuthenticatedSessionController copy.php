<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\User;
use App\Models\Vendor;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
    }

    /*protected function authenticated(Request $request, $user)
    {
        if($user->delete_status == 1)
        {
            auth()->logout();
        }

        return redirect('/check');
    }*/

    public function store(LoginRequest $request)
    {   
        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation=[];
        }
        $this->validate($request, $validation);

        $user =\Auth::user();
        if($user == 'isOwner')
        {
            $plan = plan::find($user->plan);
            if($plan)
            {
                if($plan->duration != 'unlimited')
                {
                    $datetime1 = new \DateTime($user->plan_expire_date);
                    $datetime2 = new \DateTime(date('Y-m-d'));  
                    $interval = $datetime2->diff($datetime1);
                    $days =$interval->format('%r%a');
                    if($days <= 0)
                    {
                        $user->assignplan(1);
                        
                        return redirect()->intended(RouteServiceProvider::HOME)->with('error',__('Yore plan is expired'));
                    }
                }
            }
        }

        $request->authenticate();

        $request->session()->regenerate();
        $user = Auth::user();
        $email    = $request->has('email') ? $request->email : '';
        $password    = $request->has('password') ? $request->password : '';

        $user->last_login_at = date('Y-m-d H:i:s');
        $user->save();
        
        $user       = $user;

        if(Auth::attempt([ 'email' => $email, 'password' => $password, 'is_active' => 1, 'user_status' => 1 ]))
        {
            $user = User::where('id', '=', Auth::user()->getCreatedBy())->first();

            if($user->isOwner())
            {
                $free_plan = Plan::where('price', '=', '0.0')->first();
                if($user->plan_id != $free_plan->id)
                {
                    if(date('Y-m-d') > $user->plan_expire_date)
                    {
                        $user->plan_id          = $free_plan->id;
                        $user->plan_expire_date = null;
                        $user->save();

                        $users     = User::where('parent_id', '=', Auth::user()->getCreatedBy())->get();
                        $customers = Customer::where('created_by', '=', Auth::user()->getCreatedBy())->get();
                        $vendors   = Vendor::where('created_by', '=', Auth::user()->getCreatedBy())->get();

                        $userCount = 0;
                        foreach($users as $user)
                        {
                            $userCount++;
                            $user->is_active = $free_plan->max_users == -1 || $userCount <= $free_plan->max_users ? 1 : 0;
                            $user->save();
                        }

                        $customerCount = 0;
                        foreach($customers as $customer)
                        {
                            $customerCount++;
                            $customer->is_active = $free_plan->max_customers == -1 || $customerCount <= $free_plan->max_customers ? 1 : 0;
                            $customer->save();
                        }

                        $vendorCount = 0;
                        foreach($vendors as $vendor)
                        {
                            $vendorCount++;
                            $vendor->is_active = $free_plan->max_vendors == -1 || $vendorCount <= $free_plan->max_vendors ? 1 : 0;
                            $vendor->save();
                        }

                        return redirect()->route('home')->with('error', 'Your plan expired limit is over, please upgrade your plan.');
                    }
                }
            }

            return redirect()->intended('/');
        }
        else
        {
            return redirect()->back()->with('error', __('Your Account has been Deactivated. Please contact your Administrator.'));
        }
        //return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showLoginForm($lang = '')
    {
        if ($lang == '') {
            $lang = env('DEFAULT_LANG') ?? 'en';
        }

        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }

    public function showLinkRequestForm($lang = '')
    {
        if ($lang == '') {
            $lang = env('DEFAULT_LANG') ?? 'en';
        }

        \App::setLocale($lang);

        return view('auth.forgot-password', compact('lang'));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {   
       
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
