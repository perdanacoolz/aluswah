<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AssetController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_operations');
        
        $assets = Asset::latest()->get();
        
        return Inertia::render('Assets/Index', [
            'assets' => $assets,
        ]);
    }
    
    public function store(Request $request)
    {
        Gate::authorize('manage_operations');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'condition' => 'required|in:good,damaged,lost',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        Asset::create($validated);
        
        return redirect()->back()->with('success', 'Aset ditambahkan!');
    }
    
    public function update(Request $request, Asset $asset)
    {
        Gate::authorize('manage_operations');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'condition' => 'required|in:good,damaged,lost',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $asset->update($validated);
        
        return redirect()->back()->with('success', 'Aset diupdate!');
    }
    
    public function destroy(Asset $asset)
    {
        Gate::authorize('manage_operations');
        
        $asset->delete();
        
        return redirect()->back()->with('success', 'Aset dihapus!');
    }
}
