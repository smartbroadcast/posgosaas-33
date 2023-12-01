<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('Manage Notification'))
        {
            $notifications = Notification::where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('id', 'DESC')->get();

            return view('notifications.index', compact('notifications'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create Notification'))
        {
            return view('notifications.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('Create Notification'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'from' => 'required|date|after_or_equal:' . date('d-m-Y'),
                                   'to' => 'required|date|after_or_equal:from',
                                   'color' => 'required',
                                   'description' => 'required',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $notification['from']        = date('Y-m-d', strtotime($request->input('from')));
            $notification['to']          = date('Y-m-d', strtotime($request->input('to')));
            $notification['color']       = $request->input('color');
            $notification['description'] = $request->input('description');
            $notification['created_by']  = Auth::user()->getCreatedBy();

            $notification = Notification::create($notification);

            return redirect()->route('notifications.index')->with('success', __('Notification added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Notification $notification)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Notification $notification)
    {
        if(Auth::user()->can('Edit Notification'))
        {
            return view('notifications.edit', compact('notification'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Notification $notification)
    {
        if(Auth::user()->can('Edit Notification'))
        {
            $validator = Validator::make(
                $request->all(), [
                                   'from' => 'required|date|after_or_equal:' . date('d-m-Y'),
                                   'to' => 'required|date|after_or_equal:from',
                                   'color' => 'required',
                                   'description' => 'required',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $notification['from']        = date('Y-m-d', strtotime($request->input('from')));
            $notification['to']          = date('Y-m-d', strtotime($request->input('to')));
            $notification['color']       = $request->input('color');
            $notification['description'] = $request->input('description');

            $notification->save();

            return redirect()->route('notifications.index')->with('success', __('Notification updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Notification $notification)
    {
        if(Auth::user()->can('Delete Notification'))
        {
            $notification->delete();

            return redirect()->route('notifications.index')->with('success', __('Notification deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function changeNotificationStatus(Request $request, $id)
    {
        if(Auth::user()->can('Manage Notification'))
        {
            $response = false;
            $status = $request->has('status') ? $request->status : 0;

            $notification = Notification::find($id);

            if($notification)
            {
                $notification->status = $status;
                $notification->save();

                $response = true;
            }

            echo json_encode($response);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
