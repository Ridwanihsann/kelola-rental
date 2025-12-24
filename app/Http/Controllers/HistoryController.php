<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display completed transactions
     */
    public function index(Request $request)
    {
        $query = History::with('rental.items')->recent();

        // Date range filter
        if ($request->has('from')) {
            $query->whereDate('actual_return_date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('actual_return_date', '<=', $request->to);
        }

        // Search by renter name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('rental', function ($q) use ($search) {
                $q->where('renter_name', 'like', "%{$search}%");
            });
        }

        // Calculate total revenue for filtered results
        $totalRevenue = (clone $query)->sum('final_total_price');

        $histories = $query->paginate(20);

        return view('history.index', compact('histories', 'totalRevenue'));
    }

    /**
     * Display transaction details
     */
    public function show(History $history)
    {
        $history->load('rental.items');

        return view('history.show', compact('history'));
    }
}
