<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ObektiNedvizhimosti;
use Yajra\DataTables\Facades\DataTables;

class SpisokObektov extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            // Убедитесь, что название модели совпадает с app/Models/ObektiNedvizhimosti.php
            $query = ObektiNedvizhimosti::query()->with('poruchenie');

            // Фильтр по Кадастровому номеру (из основной таблицы)
            if ($request->filled('cadastral_number')) {
                $query->where('kadastroviy_nomer', 'like', '%' . $request->cadastral_number . '%');
            }

            // Фильтр по Типу объекта (из основной таблицы)
            if ($request->filled('object_type')) {
                $query->where('tip_obekta_nedvizhimosti', $request->object_type);
            }

            // Фильтр по Входящему номеру (через связь с таблицей poruchenie)
            if ($request->filled('incoming_number')) {
                $query->whereHas('poruchenie', function($q) use ($request) {
                    $q->where('incoming_number', 'like', '%' . $request->incoming_number . '%');
                });
            }

            return DataTables::eloquent($query)
                ->addColumn('incoming_number', fn($obekt) => $obekt->poruchenie?->incoming_number)
                ->addColumn('incoming_date', fn($obekt) => $obekt->poruchenie?->incoming_date)
                ->addColumn('urr_number', fn($obekt) => $obekt->poruchenie?->urr_number)
                ->addColumn('urr_date', fn($obekt) => $obekt->poruchenie?->urr_date)
                ->addColumn('actions', function ($obekt) {
                    return '<div class="d-flex gap-1">
                                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->toJson();
        }

        return view('obekti-nedvizhimocti.spisok-obektov');
    }
}
