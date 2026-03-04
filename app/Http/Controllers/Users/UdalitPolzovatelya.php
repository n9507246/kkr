<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UdalitPolzovatelya extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Нельзя удалить текущего авторизованного пользователя');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}
