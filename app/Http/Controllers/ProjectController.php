<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'deadline' => 'required|date',
            'status' => 'in:0,1',
            'company' => 'required',
            'user_email' => 'required',
        ]);

        $validated['client_id'] = Client::where('company', $validated['company'])->first()->id;
        $validated['user_id'] = User::where('email', $validated['user_email'])->first()->id;
        unset($validated['company'], $validated['user_email']);
        $project = Project::create($validated);
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
        //
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
            'title' => 'nullable',
            'description' => 'nullable',
            'deadline' => 'date',
            'status' => 'in:0,1',
            'company' => 'nullable',
            'user_email' => 'nullable',
        ]);

        if ($request->has('company')) {
            $validated['client_id'] = Client::where('company', $validated['company'])->first()->id;
        }
        
        if ($request->has('user_email')) {
            $validated['user_id'] = User::where('email', $validated['user_email'])->first()->id;
        }
        
        unset($validated['company'], $validated['user_email']);
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
