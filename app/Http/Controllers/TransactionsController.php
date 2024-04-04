<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleBill;
use App\Models\SaleItem;
use App\Models\SaleBillDetails;
use App\Models\Stock;
use App\Http\Requests\SaleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    //SALES
    public function salesIndex()
    {
        $sales = SaleBill::with('saleItems')->orderBy('created_at', 'desc')->paginate(10);
        // dd($sales->items);
        return view('transactions.sales.index', compact('sales'));
    }

    public function salesCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
        return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $stocks = Stock::where('is_deleted', false)->get();
        $formset = []; 
        return view('transactions.sales.create', compact('stocks', 'formset'));
    }

    public function salesStore(Request $request)
    {
        // Store the sale
        $sale = new SaleBill($request->except('items'));
        $sale->company_id = auth()->user()->company_id ?? 1;
        $sale->save();

        // Store sale items
        foreach ($request->items as $item) {
            // Find the stock by its name
            $stock = Stock::where('name', $item['stock_name'])->firstOrFail();

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
        $bill = SaleBill::findOrFail($id); // Assuming your Sale model is named 'Sale'
        // dd($id, $bill);
        return view('transactions.sales.bill', compact('bill'));
    }
    

    public function salesDestroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Restore stock quantity
            foreach ($sale->items as $item) {
                $stock = Stock::where('name', $item->stock->name)->firstOrFail();
                $stock->quantity += $item->quantity;
                $stock->save();
            }

            // Delete sale and related items
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }


}
