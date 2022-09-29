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
            return redirect()->route('unauthorized');
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
            return redirect()->route('unauthorized');
        }

        $project = new Project();
        $project->title = $request->title;
        $project->description = $request->description;
        $project->deadline = $request->deadline;
        $project->status = $request->status;
        $project->client_id = Client::where('company', $request->company)->first()->id;
        $project->user_id = User::where('email', $request->user_email)->first()->id;
        $project->save();
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
            return redirect()->route('unauthorized');
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
            return redirect()->route('unauthorized');
        }

        $project->title = $request->title;
        $project->description = $request->description;
        $project->deadline = $request->deadline;
        $project->status = $request->status;
        $project->client_id = Client::where('company', $request->company)->first()->id;
        $project->user_id = User::where('email', $request->user_email)->first()->id;
        $project->save();
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
            return redirect()->route('unauthorized');
        }

        $project->delete();
        return redirect()->route('projects.index');
    }
}
