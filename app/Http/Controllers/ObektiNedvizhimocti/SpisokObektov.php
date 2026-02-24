<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Models\VidiRabot;
use App\Models\VneshniePorucheniya;

class SpisokObektov extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            // Логируем запрос для отладки
            \Log::info('AJAX запрос:', [
                'url' => $request->fullUrl(),
                'sort' => $request->get('sort'),
            ]);

            $query = KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot', 'tipObekta']);

            // ----- ФИЛЬТРЫ -----
            if ($request->filled('cadastral_number')) {
                $query->where('kadastroviy_nomer', 'like', "%{$request->cadastral_number}%");
            }

            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('vhod_nomer', 'like', "%{$request->incoming_number}%");
                });
            }

            if ($request->filled('ispolnitel')) {
                $query->where('ispolnitel', 'like', "%{$request->ispolnitel}%");
            }

            if ($request->filled('tip_obekta_id')) {
                $query->where('tip_obekta_id', $request->tip_obekta_id);
            }

            if ($request->filled('vid_rabot_id')) {
                $query->where('vid_rabot_id', $request->vid_rabot_id);
            }

            if ($request->filled('completion_date_start')) {
                $query->whereDate('data_zaversheniya', '>=', $request->completion_date_start);
            }
            if ($request->filled('completion_date_end')) {
                $query->whereDate('data_zaversheniya', '<=', $request->completion_date_end);
            }

            if ($request->filled('incoming_date')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->whereDate('vhod_data', $request->incoming_date);
                });
            }

            if ($request->filled('urr_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('urr_nomer', 'like', "%{$request->urr_number}%");
                });
            }

            if ($request->filled('urr_date')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->whereDate('urr_data', $request->urr_date);
                });
            }

            if ($request->filled('comment')) {
                $query->where('kommentariy', 'like', "%{$request->comment}%");
            }

            // ----- СОРТИРОВКА -----
            if ($request->filled('sort') && is_array($request->sort)) {
                foreach ($request->sort as $sort) {
                    $field = $sort['field'];
                    $direction = $sort['dir'] ?? 'asc';
                    
                    switch ($field) {
                        // Поля из основной таблицы
                        case 'kadastroviy_nomer':
                        case 'ispolnitel':
                        case 'data_zaversheniya':
                        case 'kommentariy':
                            $query->orderBy($field, $direction);
                            break;
                            
                        // Поле из связанной таблицы tipObekta
                        case 'tip_obekta.abbreviatura':
                            $query->orderBy(
                                TipyObektov::select('abbreviatura')
                                    ->whereColumn('tipy_obektov.id', 'kadastrovie_obekti.tip_obekta_id'),
                                $direction
                            );
                            break;
                            
                        // Поле из связанной таблицы vidiRabot
                        case 'vidi_rabot.nazvanie':
                            $query->orderBy(
                                VidiRabot::select('nazvanie')
                                    ->whereColumn('vidi_rabot.id', 'kadastrovie_obekti.vid_rabot_id'),
                                $direction
                            );
                            break;
                            
                        // Поля из связанной таблицы poruchenie
                        case 'poruchenie.vhod_nomer':
                        case 'poruchenie.vhod_data':
                        case 'poruchenie.urr_nomer':
                        case 'poruchenie.urr_data':
                            $relatedField = str_replace('poruchenie.', '', $field);
                            $query->orderBy(
                                VneshniePorucheniya::select($relatedField)
                                    ->whereColumn('vneshnie_porucheniya.id', 'kadastrovie_obekti.poruchenie_id'), // ИСПРАВЛЕНО!
                                $direction
                            );
                            break;
                            
                        default:
                            $query->orderBy($field, $direction);
                            break;
                    }
                }
            } else {
                $query->orderBy('kadastrovie_obekti.created_at', 'desc');
            }

            $size = $request->get('size', 20);
            $paginated = $query->paginate($size);

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