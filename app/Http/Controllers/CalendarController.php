<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Calendar;

class CalendarController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Calendar Event')) {

            $events    = Calendar::where('created_by', '=', \Auth::user()->getCreatedBy())->get();

            $now = date('m');
            $current_month_event = Calendar::select('id', 'start', 'end', 'title', 'created_at', 'className')->whereRaw('MONTH(start)=' . $now)->get();

            $arrEvents = [];
            foreach ($events as $event) {

                $arr['id']    = $event['id'];
                $arr['title'] = $event['title'];
                $arr['start'] = $event['start'];
                $arr['end']   = $event['end'];
                $arr['className'] = $event['className'];


                $arr['url']             = route('calendars.edit', $event['id']);

                $arrEvents[] = $arr;
            }
            // $arrEvents = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrEvents)));
            $arrEvents =  json_encode($arrEvents);

            return view('calendars.index', compact('events', 'arrEvents', 'current_month_event'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()

    {
        if (Auth::user()->can('Manage Calendar Event')) {

            return view('calendars.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if (Auth::user()->can('Create Calendar Event')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'start' => 'required',
                    'end' => 'required|after_or_equal:start',
                    // 'end_date' => 'required|after_or_equal:start_date',
                    'className' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $event                = new Calendar();
            $event->title         = $request->title;
            $event->start         = $request->start;
            $event->end           = $request->end;
            $event->className     = $request->className;
            $event->description   = $request->description;
            $event->created_by    = \Auth::user()->getCreatedBy();

            $event->save();

            return redirect()->route('calendars.index')->with('success', __('Event successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        $events    = Calendar::where('created_by', '=', \Auth::user()->getCreatedBy())->get();

        $now = date('m');
        $current_month_event = Calendar::select('id', 'start', 'end', 'title', 'created_at', 'className', 'description')->where('id', $id)->first();

        return view('calendars.show', compact('current_month_event'));
    }

    public function edit($event)
    {

        $event = Calendar::find($event);
        return view('calendars.edit', compact('event'));
    }

    public function update(Request $request, Calendar $calendar)
    {
        if (Auth::user()->can('Edit Calendar Event')) {
            if ($calendar->created_by == \Auth::user()->getCreatedBy()) {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'title' => 'required',
                        'start' => 'required',
                        'end' => 'required|after_or_equal:start',
                        'className' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $calendar->title       = $request->title;
                $calendar->start       = $request->start;
                $calendar->end         = $request->end;
                $calendar->className   = $request->className;
                $calendar->description = $request->description;
                $calendar->save();

                return redirect()->back()->with('success', __('Event successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Calendar $calendar)
    {
        $calendar->delete();

        return redirect()->back()->with('success', __('Event successfully deleted.'));
    }
}
