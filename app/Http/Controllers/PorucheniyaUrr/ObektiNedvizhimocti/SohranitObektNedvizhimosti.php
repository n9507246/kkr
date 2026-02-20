<?php

namespace App\Http\Controllers\PorucheniyaUrr\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ObektiNedvizhimosti;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueCadastralWithSoftDelete;

class SohranitObektNedvizhimosti extends Controller
{
    public function __invoke(Request $request, $id_porucheniya_urr)
    {
        // Валидация с кастомным правилом
            $obekt = $request->validate([
                'kadastroviy_nomer' => ['required', 'string', 'max:50', new UniqueCadastralWithSoftDelete($id_porucheniya_urr)],
                'tip_obekta_nedvizhimosti' => 'required|string|max:100',
                'vid_rabot' => 'nullable|string|max:100',
                'data_zaversheniya' => 'nullable|date',
                'komentarii' => 'nullable|string',

            ], [
                'kadastroviy_nomer.required' => 'Кадастровый номер обязателен для заполнения',
                'kadastroviy_nomer.max' => 'Кадастровый номер не может быть длиннее 50 символов',
                'tip_obekta_nedvizhimosti.required' => 'Тип объекта недвижимости обязателен для заполнения',
                'data_zaversheniya.date' => 'Дата завершения должна быть корректной датой',
            ]);


        $obekt['id_porucheniya_urr'] = $id_porucheniya_urr;

        // Создание нового объекта
        $obekt = ObektiNedvizhimosti::create($obekt);

        return redirect()
            ->route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', [
                'poruchenie_urr' => $id_porucheniya_urr
            ])
            ->with('success', 'Объект успешно добавлен');
    }
}
