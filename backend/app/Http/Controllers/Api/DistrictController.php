<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = District::where('is_active', true);

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('name_hi', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('division')) {
            $query->where('division', $request->query('division'));
        }

        $districts = $query->orderBy('name_en')->get();

        return response()->json([
            'status' => 'success',
            'count' => $districts->count(),
            'data' => $districts,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $district = District::with('busStops')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $district,
        ]);
    }
}
