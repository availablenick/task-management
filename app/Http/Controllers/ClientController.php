<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
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
        if ($request->user()->cannot('create', Client::class)) {
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
        if ($request->user()->cannot('create', Client::class)) {
            return redirect()->route('unauthorized');
        }

        $validated = $request->validate([
            'company' => 'required',
            'vat' => 'required|integer',
            'address' => 'required',
            'is_active' => 'boolean',
        ]);

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
        //
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
            return redirect()->route('unauthorized');
        }
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
            return redirect()->route('unauthorized');
        }

        $validated = $request->validate([
            'company' => 'nullable',
            'vat' => 'integer',
            'address' => 'nullable',
            'is_active' => 'boolean',
        ]);

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
            return redirect()->route('unauthorized');
        }

        $client->delete();
        return redirect()->route('clients.index');
    }
}
