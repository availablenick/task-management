<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create', User::class)) {
            abort(403);
        }

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', User::class)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars');
        }

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        event(new Registered($user));
        return redirect()->route('users.show', $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $user)
    {
        if ($request->user()->cannot('update', $user)) {
            abort(403);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->user()->cannot('update', $user)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars');
            Storage::disk()->delete($user->avatar_path);
        }

        $user->update($validated);
        return redirect()->route('users.show', $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->cannot('delete', $user)) {
            abort(403);
        }

        Storage::disk()->delete($user->avatar_path);
        $user->delete();
        return redirect()->route('users.index');
    }
}
