<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Models\VidiRabot;

class SpisokObektov extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            // Исправлено: Добавляем 'tipObekta' в жадную загрузку (with)
            $query = KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot', 'tipObekta']);

            // --- Фильтры ---

            // Поиск по кадастровому номеру
            if ($request->filled('cadastral_number')) {
                $query->where('kadastroviy_nomer', 'like', "%{$request->cadastral_number}%");
            }

            // Поиск по входящему номеру поручения
            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('vhod_nomer', 'like', "%{$request->incoming_number}%");
                });
            }

            // Поиск по исполнителю
            if ($request->filled('ispolnitel')) {
                $query->where('ispolnitel', 'like', "%{$request->ispolnitel}%");
            }

            // Новый фильтр: По типу объекта (если нужно фильтровать из списка)
            if ($request->filled('tip_obekta_id')) {
                $query->where('tip_obekta_id', $request->tip_obekta_id);
            }

            // Фильтр по виду работ
            if ($request->filled('vid_rabot_id')) {
                $query->where('vid_rabot_id', $request->vid_rabot_id);
            }

            // Фильтр по дате завершения (диапазон)
            if ($request->filled('completion_date_start')) {
                $query->whereDate('data_zaversheniya', '>=', $request->completion_date_start);
            }
            if ($request->filled('completion_date_end')) {
                $query->whereDate('data_zaversheniya', '<=', $request->completion_date_end);
            }

            // Фильтр по входящей дате (через связь)
            if ($request->filled('incoming_date')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->whereDate('vhod_data', $request->incoming_date);
                });
            }

            // Фильтр по номеру УРР (через связь)
            if ($request->filled('urr_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('urr_nomer', 'like', "%{$request->urr_number}%");
                });
            }

            // Фильтр по дате УРР (через связь)
            if ($request->filled('urr_date')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->whereDate('urr_data', $request->urr_date);
                });
            }

            // Фильтр по комментарию
            if ($request->filled('comment')) {
                $query->where('kommentariy', 'like', "%{$request->comment}%");
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

        $tipyObektov = TipyObektov::where('activno', true)->get();
        $vidiRabot = VidiRabot::where('activno', true)->get();

        return view('obekti-nedvizhimocti.spisok-obektov', compact('tipyObektov', 'vidiRabot'));
    }
}
