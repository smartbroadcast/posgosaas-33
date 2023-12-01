<?php

namespace App\Http\Controllers;

use App\Mail\CustomerCreate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Plan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use Illuminate\Support\Facades\Hash;
use App\Models\Utility;


class CustomerController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Customer')) {
            $customers = Customer::where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            return view('customers.index')->with('customers', $customers);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('Create Customer')) {
            return view('customers.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('Create Customer')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|max:100|unique:customers,email,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                    'phone_number' => 'required|min:10|max:15',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if (!empty($request->email)) {
                $customer_has_mail = Customer::Where('email', $request->email)->count();
                if ($customer_has_mail != 0) {
                    return redirect()->back()->with('error', __('The email has already been taken.'));
                }
            }

            $user = User::where('id', '=', Auth::user()->getCreatedBy())->first();

            $total_customer = Customer::where('created_by', '=', $user->getCreatedBy())->count();

            $plan = Plan::find($user->plan_id);

            if ($plan->max_customers == -1 || $total_customer < $plan->max_customers) {
                $customer['name']         = $request->name;
                $customer['email']        = $request->email;
                $customer['phone_number'] = $request->phone_number;
                $customer['address']      = $request->address;
                $customer['city']         = $request->city;
                $customer['state']        = $request->state;
                $customer['country']      = $request->country;
                $customer['zipcode']      = $request->zipcode;
                $customer['is_active']    = 1;
                $customer['created_by']   = $user->getCreatedBy();

                $customer = Customer::create($customer);

                try {
                    $customer->type = 'Customer'; 

                    $uArr = [
                        'app_name'  =>env('APP_NAME'),
                        'app_url'=> env('APP_URL'),
                        'customer_name' => $request->name,
                        'customer_email' =>$request->email,
                        'customer_phone_number' =>$request->phone_number,
                        'customer_address' =>$request->address,
                        'customer_country'=> $request->country,
                        'customer_zipcode'=> $request->zipcode,
                      ];
                  
                    //   Mail::to($customer->email)->send(new CustomerCreate($customer));
                      $resp = Utility::sendEmailTemplate('new_customer', [$customer->id => $customer->email], $uArr);
                //    dd($resp);

                } catch (\Exception $e) { 
                   
                    $smtp_error = "<br><span class='text-danger'>" . __('E-Mail has been not sent due to SMTP configuration') . '</span>';
                }


                return redirect()->route('customers.index')->with('success', __('Customer added successfully.') . ((isset($smtp_error)) ? $smtp_error : ''));
            } else {
                return redirect()->back()->with('error', __('Your customer limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Customer $customer)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Customer $customer)
    {
        if (Auth::user()->can('Edit Customer')) {
            return view('customers.edit', compact('customer'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Customer $customer)
    {
        if (Auth::user()->can('Edit Customer')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|max:100|unique:customers,email,' . $customer->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                    'phone_number' => 'required|min:10|max:15',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if (!empty($request->email)) {
                $customer_has_mail = Customer::whereraw('email = "' . $request->email . ' " ')->whereraw('id != ' . $customer->id . '')->count();
                if ($customer_has_mail != 0) {
                    return redirect()->back()->with('error', __('The email has already been taken.'));
                }
            }

            $customer['name']         = $request->name;
            $customer['email']        = $request->email;
            $customer['phone_number'] = $request->phone_number;
            $customer['address']      = $request->address;
            $customer['city']         = $request->city;
            $customer['state']        = $request->state;
            $customer['country']      = $request->country;
            $customer['zipcode']      = $request->zipcode;
            $customer->save();

            return redirect()->route('customers.index')->with('success', __('Customer updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Customer $customer)
    {
        if (Auth::user()->can('Delete Customer')) {
            $customer->delete();

            return redirect()->route('customers.index')->with('success', __('Customer successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function searchCustomers(Request $request)
    {
        if (Auth::user()->can('Manage Customer')) {
            $customers = [];
            $search    = $request->search;
            if ($request->ajax() && isset($search) && !empty($search)) {
                $customers = Customer::select('id as value', 'name as label', 'email')->where('is_active', '=', 1)->where('created_by', '=', Auth::user()->getCreatedBy())->Where('name', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->get();

                return json_encode($customers);
            }

            return $customers;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getCustomerEmail(Request $request)
    {
        if (Auth::user()->can('Manage Customer')) {
            $customer_email = [];
            if ($request->ajax()) {
                $customer_email = Customer::select(DB::Raw('IFNULL( email , "" ) as email'))->where('is_active', '=', 1)->where('customers.id', '=', $request->id)->where('customers.created_by', '=', Auth::user()->getCreatedBy())->get();

                return json_encode($customer_email);
            }

            return $customer_email;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function export()
    {
        $name = 'customer_' . date('Y-m-d i:h:s');
        $data = Excel::download(new CustomerExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('customers.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];
        
        $validator = \Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            
            return redirect()->back()->with('error', $messages->first());
        }
        
        $customers = (new CustomerImport())->toArray(request()->file('file'))[0];
        
        $totalCustomer = count($customers) - 1;
        $errorArray    = [];
        for ($i = 1; $i <= count($customers) - 1; $i++) {
            $customer = $customers[$i];
            
            $customerByEmail = Customer::where('email', $customer[1])->first();
           
            if (!empty($customerByEmail)) {
                $customerData = $customerByEmail;
            } else {
                $customerData = new Customer();
                // $customerData->customer_id      = $this->customerNumber();
            }
           
            $customerData->name             = $customer[0];
            $customerData->email            = $customer[1];
            $customerData->phone_number     = $customer[2];
            $customerData->address          = $customer[3];
            $customerData->city             = $customer[4];
            $customerData->state            = $customer[5];
            $customerData->country          = $customer[6];
            $customerData->zipcode          = $customer[7];
            $customerData->is_active        = 1;
            $customerData->created_by       = \Auth::user()->getCreatedBy();
         
            if (empty($customerData)) {
                $errorArray[] = $customerData;
            } else {
                $customerData->save();
            }
        }

        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
}
