<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SohranitPoruchenie extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $order = \App\Models\VneshniePorucheniya::create([
            "incoming_number" => $request['incoming_number'],
            "incoming_date" => $request['incoming_date'],
            "urr_number" => $request['urr_number'],
            "urr_date" => $request['urr_date'],
            "description" => $request['description'],
            "outgoing_number" => null,
            "outgoing_date" => null,
        ]);

        return redirect()
            ->route('porucheniya-urr.spisok-porucheniy', $order)
            ->with('success', 'Поручение успешно создано');
    }
}
