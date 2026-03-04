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

        $emailLocalPart = strtok($user->email, '@') ?: 'user';
        $emailLocalPart = preg_replace('/[^a-zA-Z0-9._-]/', '', $emailLocalPart) ?: 'user';
        $suffix = now()->format('YmdHis') . '_' . $user->id;
        $maxLocalLength = 255 - strlen('+deleted_' . $suffix . '@example.com');
        $emailLocalPart = substr($emailLocalPart, 0, max(1, $maxLocalLength));

        $user->email = $emailLocalPart . '+deleted_' . $suffix . '@example.com';
        $user->save();

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}
