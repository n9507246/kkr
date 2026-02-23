<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipyObektov;
use App\Models\VidiRabot;

class RedactirovatObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta)
    {
        $obekt = \App\Models\KadastrovieObekti::query()
                    ->with('poruchenie')
                    ->where('id', $id_obekta)
                    ->firstOrFail();

        $tipyObektov = TipyObektov::where('activno', true)->get();
        $vidiRabot = VidiRabot::where('activno', true)->get();

        return view('obekti-nedvizhimocti.redaktirovat-obekt', compact('obekt', 'tipyObektov', 'vidiRabot'));
    }
}
