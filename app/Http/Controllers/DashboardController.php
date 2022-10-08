<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $userCount = User::count();
        $clientCount = Client::count();
        $projectCount = Project::count();
        return view('dashboard.dashboard', compact('userCount', 'clientCount', 'projectCount'));
    }
}
