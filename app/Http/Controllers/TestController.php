<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $users = \App\Models\User::select('name', 'email')->get()->toArray();
        return view('test', compact('users'));
    }
}
