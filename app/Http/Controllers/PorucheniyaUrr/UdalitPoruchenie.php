<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UdalitPoruchenie extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request,string $id_poruchenia)
    {
        $poruchenie = \App\Models\VneshniePorucheniya::findOrFail($id_poruchenia);
        $poruchenie->delete();

        return redirect()
            ->route('porucheniya-urr.spisok-porucheniy')
            ->with('success', 'Поручение удалено');
    }
}
