<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KadastrovieObekti;

class SpisokObektov extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            // Загружаем связи: поручение и вид работ из справочника
            $query = KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot']);

            // --- Фильтры (имена полей из запроса можно оставить прежними,
            // но поиск ведем по новым колонкам в БД) ---

            // Поиск по кадастровому номеру
            if ($request->filled('cadastral_number')) {
                $query->where('kadastroviy_nomer', 'like', "%{$request->cadastral_number}%");
            }

            // Поиск по входящему номеру из связанной таблицы поручений
            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('vhod_nomer', 'like', "%{$request->incoming_number}%");
                });
            }

            // Поиск по исполнителю (теперь это поле в kadastrovie_obekti)
            if ($request->filled('ispolnitel')) {
                $query->where('ispolnitel', 'like', "%{$request->ispolnitel}%");
            }

            // Фильтр по виду работ (если из Tabulator летит ID)
            if ($request->filled('vid_rabot_id')) {
                $query->where('vid_rabot_id', $request->vid_rabot_id);
            }

            $size = $request->get('size', 10);
            $paginated = $query->paginate($size);

            // Возвращаем JSON для Tabulator
            return response()->json([
                'last_page' => $paginated->lastPage(),
                'data' => $paginated->items(),
                'total' => $paginated->total(),
            ]);
        }

        return view('obekti-nedvizhimocti.spisok-obektov');
    }
}
