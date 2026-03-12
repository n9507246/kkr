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
            $baseQuery = \App\Models\KadastrovieObekti::query()
                ->with([
                    'poruchenie',
                    'vidiRabot',
                    'tipObekta',
                    'ispolnitelUser',
                    'roditelskiyObekt',
                ])
                ->filter( $request->filters ?? [] );

            $totalMain = (clone $baseQuery)->whereNull('roditelskiy_obekt_id')->count();
            $totalChild = (clone $baseQuery)->whereNotNull('roditelskiy_obekt_id')->count();

            $data = $baseQuery
                ->sort( $request->sort ?? [] )
                ->paginate( $request->size ?? 10);
               
                
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
                'total_main' => $totalMain,
                'total_child' => $totalChild,
            ]);
        }

        $tipyObektov = \App\Models\TipyObektov::where('activno', true)->get();
        $vidiRabot = \App\Models\VidiRabot::where('activno', true)->get();

        return view('obekti-nedvizhimocti.spisok-obektov', compact('tipyObektov', 'vidiRabot'));
    }
}
