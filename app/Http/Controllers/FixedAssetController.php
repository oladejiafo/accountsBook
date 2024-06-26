<?php
namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasPermission('fixed_assets.access') && !auth()->user()->hasRole('Super_Admin')) {
                return redirect()->route('home')->with('error', 'Unauthorized access.');
            }
            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        // Get search query from request
        $search = $request->input('search');
    
        // Fetch fixed assets with search and paginate results
        $perPage = $request->input('per_page', 25);
        $fixedAssets = FixedAsset::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->paginate($perPage);
    
        // Return view with fixed assets and search query
        return view('fixed_assets.index', compact('fixedAssets', 'search'));
    }
    

    public function create()
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }    
        return view('fixed_assets.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

            $companyId = auth()->user()->company_id;
            //where('company_id', $companyId)->get();
    
    
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'acquisition_date' => 'required|date',
            'cost' => 'required|numeric',
            'depreciation_method' => 'nullable',
            'useful_life' => 'nullable|integer',
            'salvage_value' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'location' => 'nullable|string',
            'status' => 'required|in:active,disposed,sold,transferred',
        ]);
        
        $fixedAsset = new FixedAsset();
        
        $fixedAsset->name = $request->input('name');
        $fixedAsset->description = $request->input('description');
        $fixedAsset->acquisition_date = $request->input('acquisition_date');
        $fixedAsset->purchase_price = $request->input('cost');
        $fixedAsset->depreciation_method = $request->input('depreciation_method');
        $fixedAsset->useful_life = $request->input('useful_life');
        $fixedAsset->salvage_value = $request->input('salvage_value');
        $fixedAsset->current_value = $request->input('current_value');
        $fixedAsset->location = $request->input('location');
        $fixedAsset->status = $request->input('status');

        $fixedAsset->company_id = auth()->user()->company_id;
        $fixedAsset->save();

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset created successfully.');
    }

    public function show(FixedAsset $fixedAsset)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('fixed_assets.show', compact('fixedAsset'));
    }

    public function edit(FixedAsset $fixedAsset)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('fixed_assets.edit', compact('fixedAsset'));
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

                $companyId = auth()->user()->company_id;
       
                $request->validate([
                    'name' => 'required',
                    'description' => 'nullable',
                    'acquisition_date' => 'required|date',
                    'cost' => 'required|numeric',
                    'depreciation_method' => 'nullable',
                    'useful_life' => 'nullable|integer',
                    'salvage_value' => 'nullable|numeric',
                    'current_value' => 'nullable|numeric',
                    'location' => 'nullable|string',
                    'status' => 'required|in:active,disposed,sold,transferred',
                ]);

                // Find the role by its ID
                $fixedAsset = FixedAsset::where('company_id', $companyId)->findOrFail($fixedAsset->id);
        
                $fixedAsset->name = $request->input('name');
                $fixedAsset->description = $request->input('description');
                $fixedAsset->acquisition_date = $request->input('acquisition_date');
                $fixedAsset->purchase_price = $request->input('cost');
                $fixedAsset->depreciation_method = $request->input('depreciation_method');
                $fixedAsset->useful_life = $request->input('useful_life');
                $fixedAsset->salvage_value = $request->input('salvage_value');
                $fixedAsset->current_value = $request->input('current_value');
                $fixedAsset->location = $request->input('location');
                $fixedAsset->status = $request->input('status');
        
                $fixedAsset->save();

        $fixedAsset->update($request->all());

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset updated successfully.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

       // Load the transaction and ensure it belongs to the authenticated user's company
        $fixedAsset = FixedAsset::where('company_id', auth()->user()->company_id)->findOrFail($fixedAsset->id);

        $fixedAsset->delete();

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed asset deleted successfully.');
    }
}
