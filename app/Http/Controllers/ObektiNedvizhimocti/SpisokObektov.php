<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpisokObektov extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $spisok_obektov = \App\Models\ObektiNedvizhimosti::query()
            ->orderBy('created_at', 'desc')
            ->with('poruchenie')  // жадная загрузка
            ->paginate(15);
        return view('obekti-nedvizhimocti.spisok-obektov', compact('spisok_obektov'));
    }
}
