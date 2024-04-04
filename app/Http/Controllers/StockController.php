<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Stock;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\StockLocation;

class StockController extends Controller
{
    public function index(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
                
        $query = $request->input('search');
        $companyId = Auth::user()->company_id ?? 1;
    
        $stocks = Stock::where('company_id', $companyId)
                       ->with('category');
    
        if ($query) {
            $stocks->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                                ->orWhereHas('category', function ($categoryQuery) use ($query) {
                                    $categoryQuery->where('name', 'like', '%' . $query . '%');
                                })
                                ->orWhereHas('storeLocation', function ($locationQuery) use ($query) {
                                    $locationQuery->where('name', 'like', '%' . $query . '%');
                                });
            });
        }
    
        $stocks = $stocks->get();
        return view('inventory.index', compact('stocks'));
    }

    public function create()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = Auth::user()->company_id ?? 1;
        $title = "New Stock";
        $form = null; // Initialize the form variable
        // Get the categories specific to the logged-in user's company
        $categories = Category::where('company_id', $companyId)->pluck('name', 'id');
            
        // Get the stock locations specific to the logged-in user's company
        $stockLocations = StockLocation::where('company_id', $companyId)->get();
        return view('inventory.create', compact('title', 'form','categories', 'stockLocations'));
    }

    public function edit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = Auth::user()->company_id ?? 1;
        $title = "Edit Stock";
        $form = null; 
        // Get the categories specific to the logged-in user's company
        $categories = Category::where('company_id', $companyId)->pluck('name', 'id');
        
        // Get the stock locations specific to the logged-in user's company
        $stockLocations = StockLocation::where('company_id', $companyId)->get();
        $stock = Stock::where('company_id', $companyId)->findOrFail($id);

        return view('inventory.edit', compact('stock','title', 'form','categories', 'stockLocations'));
    }

    public function store(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Validate the form data
        $validatedData = $request->validate([
            'category' => 'required',
            'name' => 'required',
            'quantity' => 'required|numeric',
            'location' => 'required',
            'reorder' => 'nullable|numeric',
            'description' => 'nullable',
        ]);
    
        // Create and store the stock record
        $stock = new Stock();
        $stock->category_id = $validatedData['category'];
        $stock->name = $validatedData['name'];
        $stock->quantity = $validatedData['quantity'];
        $stock->store_location = $validatedData['location'];
        $stock->description = $validatedData['description'];
        $stock->reorder_point = $validatedData['reorder'];
        // Set the company_id based on the authenticated user or default to 1
        $stock->company_id = auth()->user()->company_id ?? 1;
        $stock->save();
    
        // Redirect back to the inventory page or any other desired page
        return redirect()->route('inventory')->with('success', 'Stock added successfully.');
    }
    


    public function update(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Validate the form data
        $validatedData = $request->validate([
            'category' => 'required',
            'name' => 'required',
            'quantity' => 'required|numeric',
            'reorder' => 'nullable|numeric',
            'location' => 'required',
            'description' => 'nullable',
        ]);
    
        // Find the stock by ID
        $stock = Stock::findOrFail($id);
    
        // Update the stock attributes
        $stock->category_id = $validatedData['category'];
        $stock->name = $validatedData['name'];
        $stock->quantity = $validatedData['quantity'];
        $stock->reorder_point = $validatedData['reorder'];
        $stock->store_location = $validatedData['location'];
        $stock->description = $validatedData['description'];
    
        // Set the company_id based on the authenticated user or default to 1
        $stock->company_id = auth()->user()->company_id ?? 1;
    
        // Save the changes
        $stock->save();
    
        // Redirect back to the inventory page or any other desired page
        return redirect()->route('inventory')->with('success', 'Stock updated successfully.');
    }

    public function destroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Find the stock by ID and delete it
        Stock::findOrFail($id)->delete();
    
        // Redirect back to the inventory page or any other desired page
        return redirect()->route('inventory')->with('success', 'Stock deleted successfully.');
    }
}
