<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

class SozdatPolzovatelya extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return view('users.sozdat-polzovatelya');
    }
}
