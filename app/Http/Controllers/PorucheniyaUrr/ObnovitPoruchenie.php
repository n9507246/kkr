<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use App\Models\VneshniePorucheniya;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObnovitPoruchenie extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, VneshniePorucheniya $poruchenie_urr)
    {
        $validated = $request->validate([
            'vhod_nomer' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vneshnie_porucheniya', 'vhod_nomer')->ignore($poruchenie_urr->id),
            ],
            'vhod_data' => 'required|date',
            'urr_nomer' => 'required|string|max:255',
            'urr_data' => 'required|date',
            'opisanie' => 'nullable|string',
            'ishod_nomer' => 'nullable|string|max:255',
            'ishod_data' => 'nullable|date',
        ]);

        $poruchenie_urr->update($validated);

        return redirect()
            ->route('porucheniya-urr.spisok-porucheniy')
            ->with('success', 'Поручение успешно обновлено');
    }
}
