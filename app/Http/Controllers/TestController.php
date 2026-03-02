<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        
        if ($request->ajax()) {
            
            $data = \App\Models\KadastrovieObekti::query()
                ->whereNull('roditelskiy_obekt_id')
                ->with([
                    'poruchenie',
                    'vidiRabot',
                    'tipObekta',
                    'dopolnitelnieObekti.poruchenie',
                    'dopolnitelnieObekti.vidiRabot',
                    'dopolnitelnieObekti.tipObekta',
                ])
                ->sort( $request->sort ?? [] )
                ->filter( $request->filters ?? [] )
                ->paginate( $request->size ?? 10);
               
                
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
            ]);
        }

        $tipyObektov = \App\Models\TipyObektov::where('activno', true)->get();
        $vidiRabot = \App\Models\VidiRabot::where('activno', true)->get();

        return view('test', compact('tipyObektov', 'vidiRabot'));
    }
}
