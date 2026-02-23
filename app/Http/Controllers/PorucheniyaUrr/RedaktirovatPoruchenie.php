<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedaktirovatPoruchenie extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id_porucheniya)
    {
        $poruchenie = \App\Models\VneshniePorucheniya::findOrFail($id_porucheniya);
        return view('porucheniya-urr.redaktirovat-poruchenie', ['poruchenie' => $poruchenie]);
    }
}
