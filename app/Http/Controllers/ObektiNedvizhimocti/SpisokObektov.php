<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ObektiNedvizhimosti;

class SpisokObektov extends Controller
{
    public function __invoke(Request $request)
    {




        if ($request->ajax()) {
            $query = ObektiNedvizhimosti::query()->with('poruchenie');

            // Фильтры
            if ($request->filled('cadastral_number')) {
                $query->where('kadastroviy_nomer', 'like', "%{$request->cadastral_number}%");
            }
            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('incoming_number', 'like', "%{$request->incoming_number}%");
                });
            }
            if ($request->filled('ispolnitel')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('ispolnitel', 'like', "%{$request->ispolnitel}%");
                });
            }

            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('incoming_number', 'like', "%{$request->incoming_number}%");
                });
            }

            $size = $request->get('size', 10);
            $paginated = $query->paginate($size);
            // dd($paginated   );
            // Возвращаем объект для Tabulator
            return response()->json([
                'data' => $paginated->items(),
                'page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'total' => $paginated->total(),
            ]);
        }

        return view('obekti-nedvizhimocti.spisok-obektov');
    }
}
