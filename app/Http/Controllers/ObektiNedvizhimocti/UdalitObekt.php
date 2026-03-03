<?php

namespace App\Http\Controllers\ObektiNedvizhimocti;

use App\Http\Controllers\Controller;
use App\Models\KadastrovieObekti;
use Illuminate\Http\Request;

class UdalitObekt extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id_obekta)
    {
        $obekt = KadastrovieObekti::query()->findOrFail($id_obekta);

        // При удалении родителя скрываем и дочерние объекты, чтобы не оставлять "осиротевшие" записи.
        $obekt->dopolnitelnieObekti()->delete();
        $obekt->delete();

        return redirect()
            ->route('obekti-nedvizhimosti.spisok-obektov')
            ->with('success', 'Объект удален.');
    }
}
