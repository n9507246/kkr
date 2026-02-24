<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\KadastrovieObekti::query()->with(['poruchenie', 'vidiRabot', 'tipObekta']);

            $perPage = $request->size ?? 10;

            $data = $query->paginate($perPage);
            return response()->json([
                'data' => $data->items(),
                'last_page' => $data->lastPage(),
            ]);
        }
        return view('test');
    }
}
