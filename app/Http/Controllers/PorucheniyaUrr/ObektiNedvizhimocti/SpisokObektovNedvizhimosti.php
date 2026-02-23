<?php

namespace App\Http\Controllers\PorucheniyaUrr\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpisokObektovNedvizhimosti extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id_poruchenie)
    {
        $spisok_obektov = \App\Models\KadastrovieObekti::query()
            ->where('id_porucheniya_urr', $id_poruchenie)
            ->orderBy('created_at', 'desc')
            ->get();
            // dd($spisok_obektov);
        return view('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', [
            "id_poruchenie" => $id_poruchenie,
            "spisok_obektov" => $spisok_obektov
        ]);
    }
}
