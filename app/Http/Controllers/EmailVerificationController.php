<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice()
    {

    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
     
        return redirect()->route('welcome');
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }
}
