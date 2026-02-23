<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedactirovatObekt extends Controller
{
    public function __invoke(Request $request, string $id_obekta)
    {
        $obekt = \App\Models\KadastrovieObekti::query()
                    ->where('id', $id_obekta)
                    ->firstOrFail();
        // dump($obekt);
        return view('obekti-nedvizhimocti.redaktirovat-obekt', compact('obekt'));
    }
}
