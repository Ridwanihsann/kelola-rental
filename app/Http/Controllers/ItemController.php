<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of items
     */
    public function index(Request $request)
    {
        $query = Item::query();

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['available', 'rented'])) {
            $query->where('status', $request->status);
        }

        // Search by name or code
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

    /**
     * Show the form for creating a new item
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'daily_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($validated);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $item->code);
    }

    /**
     * Display the specified item
     */
    public function show(Item $item)
    {
        // Load rental statistics
        $item->loadCount('rentals');

        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the item
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified item
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'daily_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified item
     */
    public function destroy(Item $item)
    {
        // Cannot delete if currently rented
        if ($item->status === 'rented') {
            return back()->with('error', 'Tidak dapat menghapus barang yang sedang disewa');
        }

        // Delete image if exists
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }
}
