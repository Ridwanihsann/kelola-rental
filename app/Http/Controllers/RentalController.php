<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * Show the form for creating a new rental
     */
    public function create()
    {
        return view('rentals.create');
    }

    /**
     * Store a newly created rental
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'renter_name' => 'required|string|max:255',
            'renter_phone' => 'required|string|max:20',
            'renter_ktp' => 'nullable|image|max:5120', // Max 5MB
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'items' => 'required|json',
        ]);

        // Decode items JSON
        $itemIds = json_decode($validated['items'], true);

        if (empty($itemIds)) {
            return back()->with('error', 'Pilih minimal 1 barang');
        }

        // Get available items
        $items = Item::whereIn('id', $itemIds)
            ->where('status', 'available')
            ->get();

        if ($items->count() !== count($itemIds)) {
            return back()->with('error', 'Beberapa barang sudah tidak tersedia');
        }

        // Calculate duration and total price
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $duration = $startDate->diffInDays($endDate) + 1;

        $dailyTotal = $items->sum('daily_price');
        $totalPrice = $dailyTotal * $duration;

        // Handle KTP image upload
        $ktpPath = null;
        if ($request->hasFile('renter_ktp')) {
            $ktpPath = $request->file('renter_ktp')->store('ktp', 'public');
        }

        // Create rental
        $rental = Rental::create([
            'renter_name' => $validated['renter_name'],
            'renter_phone' => $validated['renter_phone'],
            'renter_ktp' => $ktpPath,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'status' => 'active',
        ]);

        // Attach items with their current daily price
        foreach ($items as $item) {
            $rental->items()->attach($item->id, [
                'daily_price' => $item->daily_price,
            ]);

            // Mark item as rented
            $item->markAsRented();
        }

        return redirect()
            ->route('returns.index')
            ->with('success', "Penyewaan berhasil diproses. Total: Rp " . number_format($totalPrice, 0, ',', '.'));
    }
}
