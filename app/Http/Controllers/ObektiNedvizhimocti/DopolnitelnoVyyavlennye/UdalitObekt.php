<?php

namespace App\Http\Controllers\ObektiNedvizhimocti\DopolnitelnoVyyavlennye;

use App\Http\Controllers\Controller;
use App\Models\KadastrovieObekti;
use Illuminate\Http\Request;

class UdalitObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta, string $id_dopolnitelnogo_obekta)
    {
        $dopolnitelniyObekt = KadastrovieObekti::query()
            ->where('id', $id_dopolnitelnogo_obekta)
            ->where('roditelskiy_obekt_id', $id_obekta)
            ->firstOrFail();

        $dopolnitelniyObekt->delete();

        return redirect()
            ->route('obekti-nedvizhimosti.spisok-obektov')
            ->with('success', 'Дополнительно выявленный объект удален.');
    }
}
