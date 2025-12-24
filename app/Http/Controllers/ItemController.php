<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ItemController extends Controller
{
    /**
     * Display a listing of items
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('status') && in_array($request->status, ['available', 'rented'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created item with Cloudinary
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'daily_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Upload langsung ke Cloudinary
            $result = $request->file('image')->storeOnCloudinary('rental_items');
            $validated['image'] = $result->getSecurePath(); // Menyimpan URL HTTPS permanen
        }

        $item = Item::create($validated);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $item->code);
    }

    public function show(Item $item)
    {
        $item->loadCount('rentals');
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update item with Cloudinary
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'daily_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Upload foto baru ke Cloudinary
            $result = $request->file('image')->storeOnCloudinary('rental_items');
            $validated['image'] = $result->getSecurePath();
            
            // Catatan: Menghapus foto lama di Cloudinary memerlukan Public ID, 
            // Untuk pemula, membiarkan foto lama tetap di Cloudinary jauh lebih aman daripada error.
        }

        $item->update($validated);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Item $item)
    {
        if ($item->status === 'rented') {
            return back()->with('error', 'Tidak dapat menghapus barang yang sedang disewa');
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }
}