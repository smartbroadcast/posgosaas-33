<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index()
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
    
        return view('todos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                               'title' => 'required|max:120',
                           ]
        );

        if($validator->fails())
        {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $todo['title']      = $request->input('title');
        $todo['color']      = $request->input('color');
        $todo['created_by'] = Auth::user()->id;

        $todo = Todo::create($todo);

        return redirect()->back()->with('success', __('Task added successfully.'));
    }

    public function show(Todo $todo)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit(Todo $todo)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function update(Request $request, Todo $todo)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroy(Todo $todo)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function changeTodoStatus($id)
    {
        
        $todo = Todo::find($id);

        if($todo)
        {
            Todo::where('id', $id)->update(['status' => (int)!$todo->status]);
        }
    }

}
