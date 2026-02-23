<?php

namespace App\Http\Controllers\PorucheniyaUrr\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KadastrovieObekti;

class UdalitObektNedvizhimosti extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $poruchenie_urr, $obekt)
    {
        // Находим объект по ID и проверяем, что он принадлежит данному поручению
        $obekt = KadastrovieObekti::query()
            ->where('id_porucheniya_urr', $poruchenie_urr)
            ->findOrFail($obekt);

        // Мягкое удаление (если используется SoftDeletes)
        $obekt->delete();

        // Перенаправляем обратно на список объектов
        return redirect()
            ->route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', [
                'poruchenie_urr' => $poruchenie_urr
            ])
            ->with('success', 'Удалено');
    }
}
