<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
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
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create', Client::class)) {
            abort(403);
        }

        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Client::class)) {
            abort(403);
        }

        $validated = $request->validate([
            'company' => 'required',
            'vat' => 'required|integer',
            'address' => 'required',
            'is_active' => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $client = Client::create($validated);
        return redirect()->route('clients.show', $client);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Client $client)
    {
        if ($request->user()->cannot('update', $client)) {
            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        if ($request->user()->cannot('update', $client)) {
            abort(403);
        }

        $validated = $request->validate([
            'company' => 'required',
            'vat' => 'required|integer',
            'address' => 'required',
            'is_active' => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $client->update($validated);
        return redirect()->route('clients.show', $client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Client $client)
    {
        if ($request->user()->cannot('delete', $client)) {
            abort(403);
        }

        $client->delete();
        return redirect()->route('clients.index');
    }
}
