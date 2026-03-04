<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;

class RedaktirovatPolzovatelya extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user)
    {
        return view('users.redaktirovat-polzovatelya', [
            'userModel' => $user,
        ]);
    }
}
