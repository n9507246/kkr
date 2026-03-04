<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VneshniePorucheniya;
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

        // Отвязываем поручения, созданные пользователем, чтобы не нарушать FK.
        VneshniePorucheniya::query()
            ->where('sozdal_id', $user->id)
            ->update(['sozdal_id' => null]);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}
