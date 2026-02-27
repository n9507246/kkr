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
            
            $data = \App\Models\KadastrovieObekti::query()
                ->with(['poruchenie', 'vidiRabot', 'tipObekta'])
                ->sort( $request->sort ?? [] )
                ->filter( $request->filters ?? [] )
                ->paginate( $request->size ?? 10);
                
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
            ]);
        }

        $tipyObektov = TipyObektov::where('activno', true)->get();
        $vidiRabot = VidiRabot::where('activno', true)->get();

        return view('obekti-nedvizhimocti.spisok-obektov', compact('tipyObektov', 'vidiRabot'));
    }
}