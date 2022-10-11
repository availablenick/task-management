<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        return view('tasks.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'nullable',
            'project_title' => 'required',
            'title' => [
                'required',
                Rule::unique('tasks')->where(function ($query) use ($request) {
                    $project = Project::where('title', $request->project_title)->first();
                    return $query
                        ->where('title', $request->title)
                        ->where('project_id', $project->id);
                }),
            ],
        ]);

        $project = Project::where('title', $validated['project_title'])->first();
        if ($project->user_id != $request->user()->id) {
            abort(403);
        }

        $validated['project_id'] = $project->id;
        $task = Task::create($validated);
        return redirect()->route('tasks.show', $task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $task->load('project');
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Task $task)
    {
        if ($task->project->user_id != $request->user()->id) {
            abort(403);
        }

        $projects = Project::where('user_id', Auth::id())->get();
        return view('tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if ($task->project->user_id != $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'nullable',
            'title' => [
                'required',
                Rule::unique('tasks')->where(function ($query) use ($request, $task) {
                    return $query
                        ->where('title', $request->title)
                        ->where('project_id', $task->project_id);
                }),
            ],
        ]);

        $task->update($validated);
        return redirect()->route('tasks.show', $task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task)
    {
        if ($task->project->user_id != $request->user()->id) {
            abort(403);
        }

        $task->delete();
        return redirect()->route('tasks.index');
    }
}
