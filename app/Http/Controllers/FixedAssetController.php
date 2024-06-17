<?php
namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        // Get search query from request
        $search = $request->input('search');
    
        // Fetch fixed assets with search and paginate results
        $fixedAssets = FixedAsset::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->paginate(10);
    
        // Return view with fixed assets and search query
        return view('fixed_assets.index', compact('fixedAssets', 'search'));
    }
    

    public function create()
    {
        return view('fixed_assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'acquisition_date' => 'required|date',
            'cost' => 'required|numeric',
            'depreciation_method' => 'required',
            'useful_life' => 'required|integer',
            'salvage_value' => 'required|numeric',
            'current_value' => 'nullable|numeric',
            'location' => 'nullable|string',
            'status' => 'required|in:active,disposed,sold,transferred',
        ]);

        FixedAsset::create($request->all());

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset created successfully.');
    }

    public function show(FixedAsset $fixedAsset)
    {
        return view('fixed_assets.show', compact('fixedAsset'));
    }

    public function edit(FixedAsset $fixedAsset)
    {
        return view('fixed_assets.edit', compact('fixedAsset'));
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'acquisition_date' => 'required|date',
            'cost' => 'required|numeric',
            'depreciation_method' => 'required',
            'useful_life' => 'required|integer',
            'salvage_value' => 'required|numeric',
            'current_value' => 'nullable|numeric',
            'location' => 'nullable|string',
            'status' => 'required|in:active,disposed,sold,transferred',
        ]);

        $fixedAsset->update($request->all());

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset updated successfully.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete();

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset deleted successfully.');
    }
}
