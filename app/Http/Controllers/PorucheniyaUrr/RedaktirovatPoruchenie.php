<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RedaktirovatPoruchenie extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id_porucheniya)
    {
        $poruchenie = \App\Models\VneshniePorucheniya::findOrFail($id_porucheniya);

        $vhod_data = $poruchenie->vhod_data ? Carbon::parse($poruchenie->vhod_data)->format('d.m.Y') : '-';
        $urr_data = $poruchenie->urr_data ? Carbon::parse($poruchenie->urr_data)->format('d.m.Y') : '-';

        return view('porucheniya-urr.redaktirovat-poruchenie', [
            'poruchenie' => $poruchenie,
            'vhod_data' => $vhod_data,
            'urr_data' => $urr_data
        ]);
    }
}
