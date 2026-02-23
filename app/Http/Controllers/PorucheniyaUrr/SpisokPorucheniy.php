<?php

namespace App\Http\Controllers\PorucheniyaUrr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VneshniePorucheniya;
class SpisokPorucheniy extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            $query = VneshniePorucheniya::query();

            $size = $request->get('size', 10);
            $paginated = $query->orderBy('created_at', 'desc')->paginate($size);

            return response()->json([
                'last_page' => $paginated->lastPage(),
                'data' => $paginated->items(),
                'total' => $paginated->total(),
            ]);
        }

        return view('porucheniya-urr.spisok-porucheniy');
    }
}
