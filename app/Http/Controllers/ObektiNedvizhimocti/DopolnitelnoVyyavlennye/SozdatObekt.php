<?php

namespace App\Http\Controllers\ObektiNedvizhimocti\DopolnitelnoVyyavlennye;

use App\Http\Controllers\Controller;
use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Models\VidiRabot;
use Illuminate\Http\Request;

class SozdatObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta)
    {
        $roditelskiyObekt = KadastrovieObekti::query()
            ->with('poruchenie')
            ->findOrFail($id_obekta);

        $tipyObektov = TipyObektov::query()->where('activno', true)->get();
        $vidiRabot = VidiRabot::query()->where('activno', true)->get();

        return view('obekti-nedvizhimocti.dopolnitelno-vyyavlennye.sozdat-obekt', compact(
            'roditelskiyObekt',
            'tipyObektov',
            'vidiRabot'
        ));
    }
}
