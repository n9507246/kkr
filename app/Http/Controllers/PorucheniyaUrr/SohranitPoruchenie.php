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
        $validated = $request->validate([
            'incoming_number' => 'required|string|max:255|unique:vneshnie_porucheniya,vhod_nomer',
            'incoming_date' => 'required|date',
            'urr_number' => 'required|string|max:255',
            'urr_date' => 'required|date',
            'description' => 'nullable|string',
            'outgoing_number' => 'nullable|string|max:255',
            'outgoing_date' => 'nullable|date',
        ]);

        $order = \App\Models\VneshniePorucheniya::create([
            'vhod_nomer' => $validated['incoming_number'],
            'vhod_data' => $validated['incoming_date'],
            'urr_nomer' => $validated['urr_number'],
            'urr_data' => $validated['urr_date'],
            'opisanie' => $validated['description'] ?? null,
            'ishod_nomer' => $validated['outgoing_number'] ?? null,
            'ishod_data' => $validated['outgoing_date'] ?? null,
        ]);

        return redirect()
            ->route('porucheniya-urr.spisok-porucheniy', $order)
            ->with('success', 'Поручение успешно создано');
    }
}
