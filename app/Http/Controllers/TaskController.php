<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $tasks = match($filter) {
            'completed' => Task::where('completed', true)->orderBy('order')->get(),
            'incomplete' => Task::where('completed', false)->orderBy('order')->get(),
            default => Task::orderBy('order')->get(),
        };

        return view('tasks.index', compact('tasks', 'filter'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|max:255','description' => 'nullable']);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => Task::max('order') + 1,
        ]);

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|max:255','description' => 'nullable']);
        $task->update($request->only('title', 'description'));
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function toggle(Task $task)
    {
    // Flip the completed status
      $task->completed = !$task->completed;
      $task->save();

    // Redirect back to the previous page
      return response()->json(['success' => true, 'completed' => $task->completed]);
    }


    public function reorder(Request $request)
    {
        $order = $request->order; // array of task IDs
        foreach ($order as $index => $id) {
            Task::where('id', $id)->update(['order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}
