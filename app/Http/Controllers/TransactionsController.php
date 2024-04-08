<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\PurchaseBillDetails;
use App\Models\Sale;
use App\Models\SaleBill;
use App\Models\SaleItem;
use App\Models\SaleBillDetails;
use App\Models\Stock;
use App\Models\Supplier;

use App\Http\Requests\SaleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    //SALES
    public function salesIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $sales = SaleBill::where('company_id', $companyId)->with('saleItems')->orderBy('created_at', 'desc')->paginate(10);

        return view('transactions.sales.index', compact('sales'));
    }

    public function salesCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
        return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $stocks = Stock::where('company_id', $companyId)->where('is_deleted', false)->get();
        $formset = []; 
        return view('transactions.sales.create', compact('stocks', 'formset'));
    }

    public function salesStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }    
        $companyId = auth()->user()->company_id;    
        // Store the sale
        $sale = new SaleBill($request->except('items'));
        $sale->company_id = auth()->user()->company_id ?? 1;
        $sale->save();

        // Store sale items
        foreach ($request->items as $item) {
            // Find the stock by its name
            $stock = Stock::where('company_id', $companyId)->where('name', $item['stock_name'])->firstOrFail();

           // Create a new SaleItem instance with the stock_id
            $saleItem = new SaleItem([
                'stock_id' => $stock->id,
                'quantity' => $item['quantity'],
                'perprice' => $stock->price, // Assuming the price is stored in the Stock model
                'totalprice' => $item['quantity'] * $stock->price, // Calculate totalprice
            ]);
            $saleItem->company_id = auth()->user()->company_id ?? 1;
           
            // Assign the billno to the sale item
            $saleItem->billno = $sale->id; // Assuming `billno` is the primary key of `SaleBill`

            $saleItem->save();

            // Update stock quantity
            // $stock = Stock::where('name', $item['stock']['name'])->firstOrFail();
            $stock->quantity -= $item['quantity'];
            $stock->save();
        }

        // Store sale bill details
        $saleBillDetails = new SaleBillDetails($request->all());
        $saleBillDetails->company_id = auth()->user()->company_id ?? 1;
        $saleBillDetails->billno = $sale->id;
        $saleBillDetails->total = $item['quantity'] * $stock->price;

        $saleBillDetails->save();

        return redirect()->route('sales.show', $sale->id)->with('success', 'Sale created successfully.');
    }

    public function salesShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        $bill = SaleBill::where('company_id', $companyId)->findOrFail($id);

        // dd($id, $bill);
        return view('transactions.sales.bill', compact('bill'));
    }
    
    public function salesDestroy(Sale $sale)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        DB::transaction(function () use ($sale, $companyId) {
            try {
                // Restore stock quantity
                foreach ($sale->items as $item) {
                    $stock = Stock::where('company_id', $companyId)
                        ->where('name', $item->stock->name)
                        ->lockForUpdate() // Optional: Lock the row for update to prevent concurrent updates
                        ->firstOrFail();
                    $stock->quantity += $item->quantity;
                    $stock->save();
                }
        
                // Delete sale and related items
                $sale->where('company_id', $companyId)->delete();
            } catch (\Exception $e) {
                // Rollback the transaction if an exception occurs
                DB::rollBack();
                // Optionally, log the error or handle it in any other appropriate way
                throw $e;
            }
        });        

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function selectSupplier()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;
        // Return the view
        $suppliers = Supplier::where('company_id', $companyId)->get();
        return view('transactions.purchases.selectSupplier', compact('suppliers')); // Change 'select_supplier' to match your actual blade file name
    }
    
    public function purchasesCreate(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;
        $stocks = Stock::where('company_id', $companyId)->where('is_deleted', false)->get();
        
        // Fetch the selected supplier details
        $selectedSupplierName = $request->input('supplier');
        $supplier = Supplier::where('id', $selectedSupplierName)->where('company_id', $companyId)->first();
            
        return view('transactions.purchases.create', compact('stocks', 'supplier'));

    }
    
    public function purchasesStore(Request $request)
    {
       // Check authentication and company identification
       if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }    
        $companyId = auth()->user()->company_id;    
        $supplier = Supplier::where('id', $request->supplier_id)->where('company_id', $companyId)->first();
        // Store the sale
        $purchase = new PurchaseBill($request->except('items'));
        $purchase->company_id = auth()->user()->company_id ?? 1;
        $purchase->supplier_id = $supplier->id ?? 1;
        $purchase->save();

        // Store sale items
        foreach ($request->items as $item) {
            // Find the stock by its name
            $stock = Stock::where('company_id', $companyId)->where('name', $item['stock_name'])->firstOrFail();

        // Create a new SaleItem instance with the stock_id
            $purchaseItem = new PurchaseItem([
                'stock_id' => $stock->id,
                'quantity' => $item['quantity'],
                'perprice' => $stock->price, // Assuming the price is stored in the Stock model
                'totalprice' => $item['quantity'] * $stock->price, // Calculate totalprice
            ]);
            $purchaseItem->company_id = auth()->user()->company_id ?? 1;
        
            // Assign the billno to the sale item
            $purchaseItem->billno = $purchase->id; // Assuming `billno` is the primary key of `SaleBill`

            $purchaseItem->save();

            // Update stock quantity
            // $stock = Stock::where('name', $item['stock']['name'])->firstOrFail();
            $stock->quantity -= $item['quantity'];
            $stock->save();
        }

        // Store sale bill details
        $purchaseBillDetails = new PurchaseBillDetails($request->all());
        $purchaseBillDetails->company_id = auth()->user()->company_id ?? 1;
        $purchaseBillDetails->billno = $purchase->id;
        $purchaseBillDetails->total = $item['quantity'] * $stock->price;

        $purchaseBillDetails->save();

        return redirect()->route('purchase.show', $purchase->id)->with('success', 'Purchase created successfully.');
        // return redirect()->route('purchase.index')->with('success', 'Purchase added successfully');
    }
    
    public function purchasesIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $purchases = PurchaseBill::where('company_id', $companyId)->with('items')->orderBy('created_at', 'desc')->paginate(10);
       
        return view('transactions.purchases.index', compact('purchases'));
    }

    public function purchasesShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        $bill = PurchaseBill::where('company_id', $companyId)->findOrFail($id);

        // dd($id, $bill);
        return view('transactions.purchases.bill', compact('bill'));
    }

    public function purchasesDestroy(Sale $sale)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        DB::transaction(function () use ($sale, $companyId) {
            try {
                // Restore stock quantity
                foreach ($sale->items as $item) {
                    $stock = Stock::where('company_id', $companyId)
                        ->where('name', $item->stock->name)
                        ->lockForUpdate() // Optional: Lock the row for update to prevent concurrent updates
                        ->firstOrFail();
                    $stock->quantity -= $item->quantity;
                    $stock->save();
                }
        
                // Delete sale and related items
                $sale->where('company_id', $companyId)->delete();
            } catch (\Exception $e) {
                // Rollback the transaction if an exception occurs
                DB::rollBack();
                // Optionally, log the error or handle it in any other appropriate way
                throw $e;
            }
        });        

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }


    public function supplierIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('created_at', 'desc')->paginate(10);
       
        return view('transactions.suppliers.index', compact('suppliers'));
    }

    public function supplier($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;
        $supplier = Supplier::where('company_id', $companyId)->findOrFail($id);

        // Retrieve the purchases for the supplier and paginate the results
        $purchases = $supplier->purchases()->paginate(10);
    
        return view('transactions.suppliers.view', compact('supplier', 'purchases'));
    }

    public function supplierCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        return view('transactions.suppliers.create');
    }

    public function supplierStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:191',
            'contact_person' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|max:45',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Supplier::create($validatedData);

        return redirect()->route('supplier.index')->with('success', 'Supplier created successfully.');
    }

    public function supplierEdit(Supplier $supplier)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;
        return view('transactions.suppliers.edit', compact('supplier'));
    }

    public function supplierUpdate(Request $request, Supplier $supplier)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:191',
            'contact_person' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|max:45',
        ]);
    
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $supplier->update($validatedData);
    
        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }
    

    public function supplierDestroy(Supplier $supplier)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        $supplier->where('company_id', $companyId)->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully.');
    }
}
