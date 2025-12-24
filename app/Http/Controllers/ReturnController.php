<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReturnController extends Controller
{
    /**
     * Display active rentals
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $filter = $request->query('filter', '');

        $query = Rental::with('items')->where('status', 'active');

        // Apply filter based on type
        switch ($filter) {
            case 'overdue':
                // Telat: end_date < hari ini
                $query->whereRaw("date(end_date) < ?", [$today]);
                break;

            case 'today':
                // Jatuh tempo hari ini
                $query->whereRaw("date(end_date) = ?", [$today]);
                break;

            case 'notstarted':
                // Belum diambil: start_date > hari ini
                $query->whereRaw("date(start_date) > ?", [$today]);
                break;
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('renter_name', 'like', "%{$search}%")
                    ->orWhere('renter_phone', 'like', "%{$search}%");
            });
        }

        $rentals = $query->orderBy('end_date')->paginate(20);

        return view('returns.index', compact('rentals'));
    }

    /**
     * Show rental details for return processing
     */
    public function show(Rental $rental)
    {
        if ($rental->status !== 'active') {
            return redirect()->route('history.show', $rental->history)
                ->with('info', 'Rental ini sudah selesai');
        }

        $rental->load('items');

        return view('returns.show', compact('rental'));
    }

    /**
     * Process return
     */
    public function process(Request $request, Rental $rental)
    {
        if ($rental->status !== 'active') {
            return back()->with('error', 'Rental ini sudah diproses');
        }

        $history = $rental->complete();

        $message = 'Pengembalian berhasil. Total: Rp ' . number_format($history->final_total_price, 0, ',', '.');

        return redirect()
            ->route('history.show', $history)
            ->with('success', $message);
    }
}
