<?php

namespace App\Http\Controllers;

use App\Events\ProjectCreated;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with(['client', 'user'])->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create', Project::class)) {
            abort(403);
        }

        $clients = Client::all();
        $users = User::all();
        return view('projects.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Project::class)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'deadline' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value < date('Y-m-d')) {
                        $fail('The' . $attribute . 'must not be in the past');
                    }
                },
            ],
            'status' => 'in:0,1',
            'company' => 'required',
            'user_email' => 'required',
        ]);

        $validated['client_id'] = Client::where('company', $validated['company'])->first()->id;
        $user = User::where('email', $validated['user_email'])->first();
        $validated['user_id'] = $user->id;
        $project = Project::create($validated);
        // event(new ProjectCreated($project, $user));
        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->load(['client', 'user']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Project $project)
    {
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }

        $clients = Client::all();
        $users = User::all();
        $project->load(['client', 'user']);
        return view('projects.edit', compact('project', 'clients', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'deadline' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value < date('Y-m-d')) {
                        $fail('The' . $attribute . 'must not be in the past');
                    }
                },
            ],
            'status' => 'in:0,1',
            'company' => 'required',
            'user_email' => 'required',
        ]);

        if ($request->has('company')) {
            $validated['client_id'] = Client::where('company', $validated['company'])->first()->id;
        }
        
        if ($request->has('user_email')) {
            $validated['user_id'] = User::where('email', $validated['user_email'])->first()->id;
        }
        
        $project->update($validated);
        return redirect()->route('projects.show', $project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Project $project)
    {
        if ($request->user()->cannot('delete', $project)) {
            abort(403);
        }

        $project->delete();
        return redirect()->route('projects.index');
    }
}
