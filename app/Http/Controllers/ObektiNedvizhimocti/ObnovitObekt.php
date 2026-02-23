<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use App\Models\CadastralItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObnovitObekt extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $id_obekta)
    {
        $obekt = \App\Models\KadastrovieObekti::findOrFail($id_obekta);
        $validated = $request->validate([
            'kadastroviy_nomer' => 'required|string|max:50',
            'tip_obekta_id' => 'required|exists:tipy_obektov,id',
            'vid_rabot_id' => 'nullable|exists:vidi_rabot,id',
            'data_zaversheniya' => 'required|date',
            'kommentariy' => 'nullable|string',
        ]);
        // Добавляем текущего пользователя как исполнителя
        $validated['ispolnitel'] = Auth::user()->name;
        // dd($validated);
        // Обновляем объект
        $obekt->update($validated);

        // Редирект обратно на список объектов
        return redirect()
            ->route('obekti-nedvizhimosti.spisok-obektov')
            ->with('success', 'Объект успешно обновлен.');
    }
}
