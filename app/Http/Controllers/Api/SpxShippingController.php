<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpxShippingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpxShippingController extends Controller
{
    public function __construct(
        protected SpxShippingService $shippingService
    ) {}

    /**
     * Autocomplete pencarian wilayah tujuan SPX
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', '');
        $results = $this->shippingService->searchDestination($query, 25);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Hitung ongkos kirim SPX
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'destination_city' => ['required', 'string'],
            'destination_district' => ['required', 'string'],
            'total_weight_grams' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $weight = (int) ($validated['total_weight_grams'] ?? 500);
            $calculation = $this->shippingService->calculateShipping(
                $validated['destination_city'],
                $validated['destination_district'],
                $weight
            );

            return response()->json([
                'success' => true,
                'data' => $calculation,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
