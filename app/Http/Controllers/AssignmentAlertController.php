<?php

namespace App\Http\Controllers;

use App\Models\AssignmentAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentAlertController extends Controller
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
        $alerts = AssignmentAlert::all();
        return view('assignment-alerts.index', compact('alerts'));
    }

    public function note(Request $request)
    {
        $request->user()->alerts()->update(['is_noted' => true]);
    }
}
