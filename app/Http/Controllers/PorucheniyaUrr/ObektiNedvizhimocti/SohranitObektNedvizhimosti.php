<?php

namespace App\Http\Controllers\PorucheniyaUrr\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Rules\UniqueCadastralWithSoftDelete;
use Illuminate\Support\Facades\Auth;

class SohranitObektNedvizhimosti extends Controller
{
    public function __invoke(Request $request, $poruchenie_urr)
    {
        // Валидация с кастомным правилом
            $obekt = $request->validate([
                'kadastroviy_nomer' => ['required', 'string', 'max:50', new UniqueCadastralWithSoftDelete($poruchenie_urr)],
                'tip_obekta_nedvizhimosti' => 'required|string|exists:tipy_obektov,abbreviatura',
                'vid_rabot' => 'nullable|string|max:100',
                'data_zaversheniya' => 'nullable|date',
                'kommentariy' => 'nullable|string',

            ], [
                'kadastroviy_nomer.required' => 'Кадастровый номер обязателен для заполнения',
                'kadastroviy_nomer.max' => 'Кадастровый номер не может быть длиннее 50 символов',
                'tip_obekta_nedvizhimosti.required' => 'Тип объекта недвижимости обязателен для заполнения',
                'data_zaversheniya.date' => 'Дата завершения должна быть корректной датой',
            ]);


        $tipObektaId = TipyObektov::query()
            ->where('abbreviatura', $obekt['tip_obekta_nedvizhimosti'])
            ->value('id');

        $obekt['poruchenie_id'] = $poruchenie_urr;
        $obekt['tip_obekta_id'] = $tipObektaId;
        $obekt['ispolnitel_id'] = Auth::id();
        unset($obekt['tip_obekta_nedvizhimosti'], $obekt['vid_rabot']);

        // Создание нового объекта
        $obekt = KadastrovieObekti::create($obekt);

        return redirect()
            ->route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', [
                'poruchenie_urr' => $poruchenie_urr
            ])
            ->with('success', 'Объект успешно добавлен');
    }
}
