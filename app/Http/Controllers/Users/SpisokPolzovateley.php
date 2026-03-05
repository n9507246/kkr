<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SpisokPolzovateley extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query();
            $filters = $request->input('filters', []);

            $name = $filters['name'] ?? $request->input('name');
            if (!empty($name)) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            $email = $filters['email'] ?? $request->input('email');
            if (!empty($email)) {
                $query->where('email', 'like', '%' . $email . '%');
            }

            $createdFrom = $filters['created_at_start'] ?? $request->input('created_at_start');
            if (!empty($createdFrom)) {
                $query->whereDate('created_at', '>=', $createdFrom);
            }

            $createdTo = $filters['created_at_end'] ?? $request->input('created_at_end');
            if (!empty($createdTo)) {
                $query->whereDate('created_at', '<=', $createdTo);
            }

            $allowedSortFields = ['name', 'email', 'created_at'];
            $sort = $request->input('sort', []);

            if (is_array($sort) && !empty($sort)) {
                foreach ($sort as $sortItem) {
                    $field = $sortItem['field'] ?? null;
                    $direction = strtolower($sortItem['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

                    if ($field && in_array($field, $allowedSortFields, true)) {
                        $query->orderBy($field, $direction);
                    }
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $size = (int) ($request->input('size', 10));
            $users = $query->paginate($size);

            return response()->json([
                'data' => $users->items(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ]);
        }

        return view('users.spisok-polzovateley');
    }
}
