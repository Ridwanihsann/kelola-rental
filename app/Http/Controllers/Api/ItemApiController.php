<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemApiController extends Controller
{
    /**
     * Get all items with optional status filter
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Find item by QR code
     */
    public function findByCode($code)
    {
        $item = Item::where('code', $code)->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }
}
