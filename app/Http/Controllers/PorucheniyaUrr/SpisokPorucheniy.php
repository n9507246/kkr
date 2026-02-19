<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalOrder;
class SpisokPorucheniy extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $spisok_porucheniy = ExternalOrder::query()
                        ->orderBy('created_at', 'desc')
                        ->get();
        return view('porucheniya-urr.spisok-porucheniy', compact('spisok_porucheniy'));
    }
}
