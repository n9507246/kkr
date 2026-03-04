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
            $filters = $request->input('filters', []);

            // Фильтрация
            $vhodNomer = $filters['vhod_nomer'] ?? $request->input('vhod_nomer');
            if (!empty($vhodNomer)) {
                $query->where('vhod_nomer', 'like', '%' . $vhodNomer . '%');
            }

            $vhodData = $filters['vhod_data'] ?? $request->input('vhod_data');
            if (!empty($vhodData)) {
                $query->whereDate('vhod_data', $vhodData);
            }

            $urrNomer = $filters['urr_nomer'] ?? $request->input('urr_nomer');
            if (!empty($urrNomer)) {
                $query->where('urr_nomer', 'like', '%' . $urrNomer . '%');
            }

            $urrData = $filters['urr_data'] ?? $request->input('urr_data');
            if (!empty($urrData)) {
                $query->whereDate('urr_data', $urrData);
            }

            $ishodNomer = $filters['ishod_nomer'] ?? $request->input('ishod_nomer');
            if (!empty($ishodNomer)) {
                $query->where('ishod_nomer', 'like', '%' . $ishodNomer . '%');
            }

            $ishodData = $filters['ishod_data'] ?? $request->input('ishod_data');
            if (!empty($ishodData)) {
                $query->whereDate('ishod_data', $ishodData);
            }

            $opisanie = $filters['opisanie'] ?? $request->input('opisanie');
            if (!empty($opisanie)) {
                $query->where('opisanie', 'like', '%' . $opisanie . '%');
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
