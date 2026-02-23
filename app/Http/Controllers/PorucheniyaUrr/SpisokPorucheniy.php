<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VneshniePorucheniya;
class SpisokPorucheniy extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            $query = VneshniePorucheniya::query();

            // Фильтрация
            if ($request->filled('vhod_nomer')) {
                $query->where('vhod_nomer', 'like', '%' . $request->vhod_nomer . '%');
            }
            if ($request->filled('vhod_data')) {
                $query->whereDate('vhod_data', $request->vhod_data);
            }
            if ($request->filled('urr_nomer')) {
                $query->where('urr_nomer', 'like', '%' . $request->urr_nomer . '%');
            }
            if ($request->filled('urr_data')) {
                $query->whereDate('urr_data', $request->urr_data);
            }
            if ($request->filled('ishod_nomer')) {
                $query->where('ishod_nomer', 'like', '%' . $request->ishod_nomer . '%');
            }
            if ($request->filled('ishod_data')) {
                $query->whereDate('ishod_data', $request->ishod_data);
            }
            if ($request->filled('opisanie')) {
                $query->where('opisanie', 'like', '%' . $request->opisanie . '%');
            }

            // Сортировка
            if ($request->has('sort') && is_array($request->sort)) {
                foreach ($request->sort as $sort) {
                    $query->orderBy($sort['field'], $sort['dir']);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $size = $request->get('size', 20);
            $paginated = $query->paginate($size);

            return response()->json([
                'last_page' => $paginated->lastPage(),
                'data' => $paginated->items(),
                'total' => $paginated->total(),
            ]);
        }

        return view('porucheniya-urr.spisok-porucheniy');
    }
}
