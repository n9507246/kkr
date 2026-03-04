<?php

namespace App\Http\Controllers\ObektiNedvizhimocti\DopolnitelnoVyyavlennye;

use App\Http\Controllers\Controller;
use App\Models\KadastrovieObekti;
use App\Models\TipyObektov;
use App\Models\VidiRabot;
use Illuminate\Http\Request;

class RedactirovatObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta, string $id_dopolnitelnogo_obekta)
    {
        $obekt = KadastrovieObekti::query()
            ->with(['poruchenie', 'roditelskiyObekt'])
            ->where('id', $id_dopolnitelnogo_obekta)
            ->where('roditelskiy_obekt_id', $id_obekta)
            ->firstOrFail();

        $tipyObektov = TipyObektov::query()->where('activno', true)->get();
        $vidiRabot = VidiRabot::query()->where('activno', true)->get();

        return view('obekti-nedvizhimocti.dopolnitelno-vyyavlennye.redaktirovat-obekt', compact('obekt', 'tipyObektov', 'vidiRabot'));
    }
}
