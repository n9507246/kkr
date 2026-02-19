<?php

namespace App\Http\Controllers\PorucheniyaUrr\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ObektiNedvizhimosti;

class SohranitObektNedvizhimosti extends Controller
{
    public function __invoke(Request $request, $id_porucheniya_urr)
    {

        // dd($request);

        ObektiNedvizhimosti::query()->create([
            'id_porucheniya_urr' => $id_porucheniya_urr,
            'kadastroviy_nomer' => $request->kadastroviy_nomer,
            'tip_obekta_nedvizhimosti' => $request->vid_rabot
        ]);

        return redirect()->route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', [
            'poruchenie_urr' => $id_porucheniya_urr
        ])->with('success', 'Готово');
    }
}