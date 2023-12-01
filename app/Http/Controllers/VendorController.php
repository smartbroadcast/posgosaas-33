<?php

namespace App\Http\Controllers;

use App\Mail\VendorCreate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\Vendor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorExport;
use App\Imports\VendorImport;
use App\Models\Utility;

class VendorController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Vendor')) {
            $vendors = Vendor::where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            return view('vendors.index')->with('vendors', $vendors);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('Create Vendor')) {
            return view('vendors.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('Create Vendor')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|max:100|unique:vendors,email,NULL,id,created_by,' . Auth::user()->getCreatedBy(),
                    'phone_number' => 'required|min:10|max:15',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if (!empty($request->email)) {
                $vendor_has_mail = Vendor::where('email', $request->email)->count();
                if ($vendor_has_mail != 0) {
                    return redirect()->back()->with('error', __('The email has been already taken.'));
                }
            }

            $user = User::where('id', '=', Auth::user()->getCreatedBy())->first();

            $total_vendor = Vendor::where('created_by', '=', $user->getCreatedBy())->count();

            $plan = Plan::find($user->plan_id);

            if ($plan->max_vendors == -1 || $total_vendor < $plan->max_vendors) {
                $vendor['name']         = $request->name;
                $vendor['email']        = $request->email;
                $vendor['phone_number'] = $request->phone_number;
                $vendor['address']      = $request->address;
                $vendor['city']         = $request->city;
                $vendor['state']        = $request->state;
                $vendor['country']      = $request->country;
                $vendor['zipcode']      = $request->zipcode;
                $vendor['is_active']    = 1;
                $vendor['created_by']   = $user->getCreatedBy();

                $vendor = Vendor::create($vendor);

               
                    $vendor->type = 'Vendor';  

                    $uArr = [   
                        'app_name'  =>env('APP_NAME'),
                        'app_url'=> env('APP_URL'),
                        'vendor_name' => $request->name,
                        'vendor_email' =>$request->email,
                        'vendor_phone_number' =>$request->phone_number,
                        'vendor_address' =>$request->address,
                        'vendor_country'=> $request->country,
                        'vendor_zipcode'=> $request->zipcode,
                      ];
                
                    
                    $resp = Utility::sendEmailTemplate('new_vendor', [$vendor->id => $vendor->email], $uArr);

                

                return redirect()->route('vendors.index')->with('success', __('Vendor added successfully.') . ((isset($smtp_error)) ? $smtp_error : ''));
            } 
           
            else {
                return redirect()->back()->with('error', __('Your vendor limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Vendor $vendor)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Vendor $vendor)
    {
        if (Auth::user()->can('Edit Vendor')) {
            return view('vendors.edit', compact('vendor'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Vendor $vendor)
    {
        if (Auth::user()->can('Edit Vendor')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|max:100|unique:vendors,email,' . $vendor->id . ',id,created_by,' . Auth::user()->getCreatedBy(),
                    'phone_number' => 'required|min:10|max:15',
                    // numeric
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if (!empty($request->email)) {
                $vendor_has_mail = Vendor::whereraw('email = "' . $request->email . ' " ')->whereraw('id != ' . $vendor->id . '')->count();
                if ($vendor_has_mail != 0) {
                    return redirect()->back()->with('error', __('The email has been already taken.'));
                }
            }

            $vendor['name']         = $request->name;
            $vendor['email']        = $request->email;
            $vendor['phone_number'] = $request->phone_number;
            $vendor['address']      = $request->address;
            $vendor['city']         = $request->city;
            $vendor['state']        = $request->state;
            $vendor['country']      = $request->country;
            $vendor['zipcode']      = $request->zipcode;
            $vendor->save();

            return redirect()->route('vendors.index')->with('success', __('Vendor updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Vendor $vendor)
    {
        if (Auth::user()->can('Delete Vendor')) {
            $vendor->delete();

            return redirect()->route('vendors.index')->with('success', __('Vendor deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function searchVendors(Request $request)
    {
        if (Auth::user()->can('Manage Vendor')) {
            $vendors = [];
            $search  = $request->search;
            if ($request->ajax() && isset($search) && !empty($search)) {
                $vendors = Vendor::select('id as value', 'name as label', 'email')->where('is_active', '=', 1)->where('created_by', '=', Auth::user()->getCreatedBy())->Where('name', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->get();

                return json_encode($vendors);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function export()
    {
        $name = 'vendor_' . date('Y-m-d i:h:s');
        $data = Excel::download(new VendorExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('vendors.import');
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
        
        $vendors = (new VendorImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($vendors) - 1;
        $errorArray    = [];
        for ($i = 1; $i <= count($vendors) - 1; $i++) {
            $vendor = $vendors[$i];

            $vendorByEmail = Vendor::where('email', $vendor[1])->first();

            if (!empty($vendorByEmail)) {
                $vendorData = $vendorByEmail;
            } else {
                $vendorData            = new Vendor();
                // $vendorData->vender_id = $this->venderNumber();
            }


            $vendorData->name             = $vendor[0];
            $vendorData->email            = $vendor[1];
            $vendorData->phone_number     = $vendor[2];
            $vendorData->address          = $vendor[3];
            $vendorData->city             = $vendor[4];
            $vendorData->state            = $vendor[5];
            $vendorData->country          = $vendor[6];
            $vendorData->zipcode          = $vendor[7];
            $vendorData->is_active        = 1;
            $vendorData->created_by       = \Auth::user()->getCreatedBy();
            if (empty($vendorData)) {
                $errorArray[] = $vendorData;
            } else {
                $vendorData->save();
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
