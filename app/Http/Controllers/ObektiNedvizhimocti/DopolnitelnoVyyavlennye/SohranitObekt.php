<?php

namespace App\Http\Controllers\ObektiNedvizhimocti\DopolnitelnoVyyavlennye;

use App\Http\Controllers\Controller;
use App\Models\KadastrovieObekti;
use App\Rules\UniqueCadastralWithSoftDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SohranitObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta)
    {
        $roditelskiyObekt = KadastrovieObekti::query()->findOrFail($id_obekta);

        $validated = $request->validate([
            'kadastroviy_nomer' => [
                'required',
                'string',
                'max:50',
                new UniqueCadastralWithSoftDelete($roditelskiyObekt->poruchenie_id),
            ],
            'tip_obekta_id' => 'required|exists:tipy_obektov,id',
            'vid_rabot_id' => 'nullable|exists:vidi_rabot,id',
            'data_zaversheniya' => 'nullable|date',
            'kommentariy' => 'nullable|string',
        ]);

        KadastrovieObekti::create([
            'poruchenie_id' => $roditelskiyObekt->poruchenie_id,
            'roditelskiy_obekt_id' => $roditelskiyObekt->id,
            'kadastroviy_nomer' => $validated['kadastroviy_nomer'],
            'tip_obekta_id' => $validated['tip_obekta_id'],
            'vid_rabot_id' => $validated['vid_rabot_id'] ?? null,
            'data_zaversheniya' => $validated['data_zaversheniya'] ?? null,
            'ispolnitel_id' => Auth::id(),
            'kommentariy' => $validated['kommentariy'] ?? null,
        ]);

        return redirect()
            ->route('obekti-nedvizhimosti.spisok-obektov'   , ['id_obekta' => $roditelskiyObekt->id])
            ->with('success', 'Дополнительно выявленный объект успешно создан');
    }
}
