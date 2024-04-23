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
use App\Models\ReturnTransaction;
use App\Models\ReturnProduct;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionAccountMapping;
use App\Models\ChartOfAccount;

use App\Notifications\ReturnProcessedNotification;  
use App\Notifications\ReturnApprovalNotification;

use App\Http\Requests\SaleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $sales = SaleBill::where('company_id', $companyId)->with('saleItems')->orderBy('created_at', 'desc')->paginate(15);

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

    public function salesEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;

        $sale = SaleBill::where('company_id', $companyId)->findOrFail($id); 
        // Fetch any other data you may need for the form
        $stocks = Stock::where('company_id', $companyId)->get();
        // Pass the sale and other data to the edit view
        return view('transactions.sales.edit', compact('sale', 'stocks'));
    }

    public function salesUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string', 
            'email' => 'required|email',  
            'contact' => 'nullable|string',  
            'address' => 'nullable|string',   
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                }),
            ],
            'items.*.quantity' => 'required|numeric|min:1',
        ]);
    
        // Find the sale by ID
        $sale = SaleBill::where('company_id', $companyId)->findOrFail($id);
    
        // Update the sale with the validated data
        $sale->update($validatedData);
    
        // Delete existing sale items
        // $sale->items()->where('company_id', $companyId)->delete();
   
        // Update sale items
        foreach ($request->items as $item) {
            // Find the stock by its ID
            $stock = Stock::where('company_id', $companyId)->findOrFail($item['stock_id']);
    
            // $stock = Stock::where('company_id', $companyId)->where('name', $item['stock_name'])->firstOrFail();

            // Check if the item exists, and update its quantity or any other relevant field
            $saleItem = SaleItem::where('sale_id', $sale->id)
                                ->where('stock_id', $stock->id)
                                ->first();
        
            if ($saleItem) {
                // Update the quantity or any other relevant field
                $saleItem->update([
                    'quantity' => $item['quantity'],
                    'stock_id' => $stock->id,
                    'perprice' => $stock->price, 
                    'totalprice' => $item['quantity'] * $stock->price,
                ]);
        
            } else {
                // Create a new SaleItem instance with the stock_id
                $saleItem = new SaleItem([
                    'stock_id' => $stock->id,
                    'quantity' => $item['quantity'],
                    'perprice' => $stock->price, // Assuming the price is stored in the Stock model
                    'totalprice' => $item['quantity'] * $stock->price, // Calculate totalprice
                ]);
                $saleItem->company_id = $companyId;
        
                // Assign the billno to the sale item
                $saleItem->billno = $sale->id; // Assuming `billno` is the primary key of `SaleBill`
        
                $saleItem->save();
            }
    
            // Update stock quantity
            $stock->quantity -= $item['quantity'];
            $stock->save();
        }
    
        // Update sale bill details
        $saleBillDetails = SaleBillDetails::where('company_id', $companyId)->where('billno', $sale->id)->firstOrFail();
        $saleBillDetails->company_id = $companyId;
        $saleBillDetails->billno = $sale->id;
        // $saleBillDetails->total = $item['quantity'] * $stock->price;
        $saleBillDetails->total = $sale->items()->sum('totalprice'); // Recalculate total

        $saleBillDetails->save();
    
        // Retrieve the existing transaction entries for sales
        $existingEntries = Transaction::where('name', 'Sales')
            ->where('company_id', $companyId)
            ->where('type', 'Sales')
            ->where('reference_number',$sale->id)
            ->get();

        // // Delete the existing transaction entries
        // foreach ($existingEntries as $entry) {
        //     $entry->delete();
        // }

        // Recreate the transaction entries based on the updated sales mapping
        $updatedSalesMapping = TransactionAccountMapping::where('transaction_type', 'Sales')
            ->where('company_id', $companyId)
            ->get();

        foreach ($updatedSalesMapping as $mapping) {
            // Retrieve the accounts associated with the updated mapping
            $debitAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->debit_account_id)
                ->first();

            $creditAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->credit_account_id)
                ->first();

            if ($debitAccount && $creditAccount) {
                // Create debit transaction entry
                $debitTransaction = new Transaction();
                $debitTransaction->reference_number = $sale->id;
                $debitTransaction->account_id = $mapping->debit_account_id;
                $debitTransaction->amount = $saleBillDetails->total;
                $debitTransaction->type = $debitAccount->type; 
                $debitTransaction->date = date('Y-m-d');
                $debitTransaction->company_id = $companyId;
                $debitTransaction->name = "Sales";
                $debitTransaction->description = "Sales transaction from customer";
                $debitTransaction->update();

                // Create credit transaction entry
                $creditTransaction = new Transaction();
                $creditTransaction->reference_number = $sale->id;
                $creditTransaction->account_id = $mapping->credit_account_id;
                $creditTransaction->amount = $saleBillDetails->total;
                $creditTransaction->type = $creditAccount->type; 
                $creditTransaction->date = date('Y-m-d');
                $creditTransaction->company_id = $companyId;
                $creditTransaction->name = "Sales";
                $creditTransaction->description = "Sales transaction from customer";
                $creditTransaction->update();
            }
        }
                
        // Redirect the user to the sales index page or show a success message
        return redirect()->route('transactions.sales.index')->with('success', 'Sale updated successfully');
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
        $sale->paid_at = date(now());
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

        // Create transaction entries based on transaction mapping for sales
        $salesMapping = TransactionAccountMapping::where('transaction_type', 'sales')
            ->where('company_id', $companyId)
            ->get();

        foreach ($salesMapping as $mapping) {
            // Retrieve the accounts associated with the mapping
            $debitAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->debit_account_id)
                ->first();

            $creditAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->credit_account_id)
                ->first();

            if ($debitAccount && $creditAccount) {
                // Create debit transaction entry
                $debitTransaction = new Transaction();
                $debitTransaction->reference_number = $sale->id;
                $debitTransaction->account_id = $mapping->debit_account_id;
                $debitTransaction->amount = $saleBillDetails->total;
                $debitTransaction->type = $debitAccount->type; 
                $debitTransaction->date = date('Y-m-d');
                $debitTransaction->company_id = $companyId;
                $debitTransaction->name = "Sales";
                $debitTransaction->description = "Sales transaction from customer";
                $debitTransaction->save();

                // Create credit transaction entry
                $creditTransaction = new Transaction();
                $creditTransaction->reference_number = $sale->id;
                $creditTransaction->account_id = $mapping->credit_account_id;
                $creditTransaction->amount = $saleBillDetails->total;
                $creditTransaction->type = $creditAccount->type; 
                $creditTransaction->date = date('Y-m-d');
                $creditTransaction->company_id = $companyId;
                $creditTransaction->name = "Sales";
                $creditTransaction->description = "Sales transaction from customer";
                $creditTransaction->save();
            }
        }

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
    
    public function showReturnsForm()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
       
        // Retrieve all return transactions
        $returnTransactions = ReturnTransaction::where('company_id', $companyId)->get();
    
        return view('transactions.returns.show', compact('returnTransactions'));
    }
    public function processReturn(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        // Validate the request data
        $validatedData = $request->validate([
            'return_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'return_status' => 'required|in:Pending,Authorized,Received,Refunded,Completed',
            'reason_for_return' => 'required|string',
            'refund_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
            'carrier' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',

            'product_id' => 'required|array',
            'product_id.*' => 'exists:stocks,id',
            'quantity' => 'required|array',
            'quantity.*' => 'numeric|min:1',
            'condition' => 'required|array',
            'condition.*' => 'in:Resalable,Used,Damaged',
        ]);
    
        // Create a new return transaction record
        $returnTransaction = ReturnTransaction::create([
            // Assign other return transaction fields from the validated data
            'company_id' => auth()->user()->company_id,
            'return_date' => $validatedData['return_date'],
            'customer_id' => $validatedData['customer_id'],
            'return_status' => $validatedData['return_status'],
            'reason_for_return' => $validatedData['reason_for_return'],
            'refund_amount' => $validatedData['refund_amount'],
            'payment_method' => $validatedData['payment_method'],
            'transaction_id' => $validatedData['transaction_id'],
            'carrier' => $validatedData['carrier'],
            'tracking_number' => $validatedData['tracking_number'],
            'shipping_cost' => $validatedData['shipping_cost'],
            'notes' => $validatedData['notes'],
            'approval_required' => $request->has('approval_required') ? 1 : 0,
        ]);
    
        // Associate return products with the return transaction
        foreach ($validatedData['product_id'] as $key => $productId) {
            ReturnProduct::create([
                'return_transaction_id' => $returnTransaction->id,
                'product_id' => $productId,
                'quantity' => $validatedData['quantity'][$key],
                'condition' => $validatedData['condition'][$key],
                'product_name' => $validatedData['name'][$key], // Assuming 'name' is passed in the form
                // Other fields as needed
                'unit_price' => $validatedData['price'][$key], // Fetch product price from database
                'company_id' => $companyId, 

            ]);
        
            // Update inventory levels
            $product = Stock::where('company_id', $companyId)->findOrFail($productId);
            $product->quantity += $validatedData['quantity'][$key];
            $product->save();
        }
            
        // Approval logic goes here
        if ($request->has('approval_required')) {
            // Update return transaction status to pending approval
            $returnTransaction->update([
                'approval_status' => 'Pending',
            ]);
    
            // Notify the approver here
            // Fetch all email addresses of users who have the authority to approve returns
            $approversEmails = User::where('role', 'approver')->pluck('email')->toArray();
    
            // Notify all the approvers
            \Mail::to($approversEmails)->send(new ReturnApprovalNotification($returnTransaction));
    
            return redirect()->back()->with('success', 'Return request submitted for approval.');
        } else {
            // No approval required, continue with refund process
    
            // Calculate refund amount based on return products
            $totalRefundAmount = 0;
            foreach ($returnTransaction->returnProducts as $returnProduct) {
                // Calculate refund amount for each product (e.g., based on quantity and product price)
                $refundAmountForProduct = $returnProduct->quantity * $returnProduct->unit_price;
                $totalRefundAmount += $refundAmountForProduct;
            }
    
            // Update customer balance (if applicable)
            $customer = $returnTransaction->customer;
            if ($customer) {
                $customer->balance += $totalRefundAmount;
                $customer->save();
            }
    
            // Record refund transaction
            RefundTransaction::create([
                'return_transaction_id' => $returnTransaction->id,
                'amount' => $totalRefundAmount,
                'payment_method' => 'Credit Card', // Example payment method
                'transaction_id' => 'REF123456', // Example transaction ID
                'refund_date' => now(), // Example refund date
                'notes' => 'Refund processed successfully', // Example notes
                'company_id' => $companyId,
            ]);
    
            // Update return status
            $returnTransaction->update([
                'return_status' => 'Completed',
            ]);
    
            // Notification logic goes here
            $approversEmails = User::where('role', 'approver')->pluck('email')->toArray();
            \Mail::to($approversEmails)->send(new ReturnProcessedNotification($returnTransaction));
    
            // Notify the customer
            $customerEmail = $returnTransaction->customer->email;
            \Mail::to($customerEmail)->send(new ReturnProcessedNotification($returnTransaction));
    
            // Reverse sales transactions (if applicable)
            foreach ($returnTransaction->returnProducts as $returnProduct) {
                // Add conditions to identify the original sales transaction
                $originalSalesTransaction = SaleItem::where([
                    'stock_id' => $returnProduct->product_id,
                    'company_id' => $companyId,
                ])->first();
    
                // Update fields to reflect the return, such as quantity or amount
                if ($originalSalesTransaction) {
                    $originalSalesTransaction->update([
                        'quantity' => $originalSalesTransaction->quantity - $returnProduct->quantity,
                        'totalprice' => $originalSalesTransaction->totalprice - ($returnProduct->unit_price * $returnProduct->quantity),
                    ]);
                }
            }
    
            // Reverse payments (if applicable)
            if ($returnTransaction->payment_method === 'Credit Card') {
                // If payment method is credit card, initiate refund through payment gateway
                // $paymentGateway = new PaymentGateway(); 
                // $refundResponse = $paymentGateway->refund($returnTransaction->transaction_id, $totalRefundAmount);
                if ($refundResponse->success) {
                    // Payment reversal successful
                    // You may update the refund transaction status or log refund details
                } else {
                    // Payment reversal failed
                    // Handle error scenario, log error, display message, etc.
                }
            } else {
                // Handle other payment methods (e.g., store credit, cash refund)
                if ($returnTransaction->payment_method === 'Cash') {
                    // For cash refunds, update refund transaction status and log refund details
                    // $cashRefund = new CashRefund(); 
                    // $cashRefund->processRefund($returnTransaction, $totalRefundAmount);
                } elseif ($returnTransaction->payment_method === 'Bank Transfer') {
                    // For bank transfer refunds, update refund transaction status and log refund details
                    // $bankTransferRefund = new BankTransferRefund(); 
                    // $bankTransferRefund->processRefund($returnTransaction, $totalRefundAmount);
                } else {
                    // Handle other payment methods (e.g., store credit)
                    // Update refund transaction status or log refund details as necessary
                }
            }
    
            // Update financial reporting (if applicable)
            // Code to update financial reporting goes here
    
            return redirect()->back()->with('success', 'Return processed successfully.');
        }
    }
    
    public function fetchCustomerTransactions(Request $request)
    {
        $customerName = $request->input('name');
    
        $customers = Customer::where('name', 'LIKE', '%' . $customerName . '%')->get();

        if ($customers->isNotEmpty()) {
            // If customers are found, retrieve their IDs and transactions
            $customerIds = $customers->pluck('id')->toArray();
            $transactions = Transaction::whereIn('customer_id', $customerIds)->get(); // Adjust this according to your actual relationship
            
            // Return the transactions or any other data you need in the response
            return response()->json(['customers' => $customers, 'transactions' => $transactions]);
        } else {
            // If no customers are found, return an empty response or an error message
            return response()->json(['error' => 'No matching customers found'], 404);
        }

        // $customerId = $request->input('customerId');
    
        // // Retrieve the customer's transactions
        // $customer = Customer::find($customerId);
        // $transactions = $customer->transactions()->orderBy('created_at', 'desc')->limit(5)->get();
    
        // // Prepare the transactions data to be returned as JSON
        // $formattedTransactions = $transactions->map(function ($transaction) {
        //     return [
        //         'id' => $transaction->id,
        //         'date' => $transaction->created_at->format('m-d-Y'), // Format the date as needed
        //         'description' => $transaction->description, // Assuming there's a 'description' field in your transaction model
        //     ];
        // });
    
        // return response()->json($formattedTransactions);
    }

    public function returnCustomers(Request $request)
    {
        $term = $request->input('name');

        // Query the database to find customers whose names start with the given term
        $customers = Customer::where('name', 'like', $term . '%')->limit(10)->get();
    
        // Prepare the data in the format required by the JavaScript code
        $formattedCustomers = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'email' => $customer->email,
                'transactions' => $customer->transactions()->where('created_at', '>=', now()->subMonths(3))->get()->pluck('formatted_transaction')->toArray(),
            ];
        });
    
        return response()->json($formattedCustomers);
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

        // Create transaction entries based on transaction mapping for sales
        $purchaseMapping = TransactionAccountMapping::where('transaction_type', 'Purchase')
            ->where('company_id', $companyId)
            ->get();

        foreach ($purchaseMapping as $mapping) {
            // Retrieve the accounts associated with the mapping
            $debitAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->debit_account_id)
                ->first();

            $creditAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->credit_account_id)
                ->first();

            if ($debitAccount && $creditAccount) {
                // Create debit transaction entry
                $debitTransaction = new Transaction();
                $debitTransaction->reference_number = $purchase->id;
                $debitTransaction->account_id = $mapping->debit_account_id;
                $debitTransaction->amount = $purchaseBillDetails->total;
                $debitTransaction->type = $debitAccount->type; 
                $debitTransaction->date = date('Y-m-d');
                $debitTransaction->company_id = $companyId;
                $debitTransaction->name = "Purchase";
                $debitTransaction->description = "Purchase transaction from supplier";
                $debitTransaction->save();

                // Create credit transaction entry
                $creditTransaction = new Transaction();
                $creditTransaction->reference_number = $purchase->id;
                $creditTransaction->account_id = $mapping->credit_account_id;
                $creditTransaction->amount = $purchaseBillDetails->total;
                $creditTransaction->type = $creditAccount->type; 
                $creditTransaction->date = date('Y-m-d');
                $creditTransaction->company_id = $companyId;
                $creditTransaction->name = "Purchase";
                $creditTransaction->description = "Purchase transaction from supplier";
                $creditTransaction->save();
            }
        }

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
        $purchases = PurchaseBill::where('company_id', $companyId)->with('items')->orderBy('created_at', 'desc')->paginate(15);
       
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
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('created_at', 'desc')->paginate(15);
       
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
        $purchases = $supplier->purchases()->paginate(15);
    
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

    public function fetchCustomerDetails(Request $request)
    {
        $customerName = $request->get('name');
        $customers = Customer::where('name', 'like', '%' . $customerName . '%')->get();
    
        // Return the first matching customer (or null if none found)
        $customer = $customers->first();
    
        return response()->json($customer);
    }

    public function customersIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $customers = Customer::query();
        
        if ($query) {
            $customers->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('phone', 'like', '%' . $query . '%')
                        ->orWhere('email', 'like', '%' . $query . '%');
                });
        } else {
            // Continue chaining methods on the $customers query builder instance
            $customers->where('company_id', $companyId);
        }
        
        // Execute the query and fetch the results
        $customers = $customers->paginate(15);        

        // $customers = Customer::where('company_id', $companyId)->get();
        return view('transactions.customers.index', compact('customers'));
    }

    public function customersCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        return view('transactions.customers.create');
    }

    public function customersShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $customer = Customer::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $sales = $customer->sales()->paginate(15);
        return view('transactions.customers.view', compact('customer','sales'));
    }
    
    public function customersStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,NULL,id,company_id,' . auth()->user()->company_id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'billing_address' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'customer_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:255',
            // 'tax_exempt' => 'nullable|boolean',
        ]);
        // $validatedData = $request->all();
        $validatedData['tax_exempt'] = $request->has('tax_exempt') ? 1 : 0;

        $validatedData['company_id'] = auth()->user()->company_id;
        Customer::create($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function customersEdit(Customer $customer)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('transactions.customers.edit', compact('customer'));
    }

    public function customersUpdate(Request $request, Customer $customer)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id . ',id,company_id,' . auth()->user()->company_id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'billing_address' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'customer_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:255',
            // 'tax_exempt' => 'nullable|boolean',
        ]);
        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['tax_exempt'] = $request->has('tax_exempt') ? 1 : 0;
        $customer->update($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function customersDestroy(Customer $customer)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        $customer->where('company_id', $companyId)->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function paymentsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        
        $query = $request->input('search');
        $payments = Payment::query();

        if ($query) {
            $payments->where('company_id', $companyId)
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('id', 'like', '%' . $query . '%')
                    ->orWhere('payment_date', 'like', '%' . $query . '%')
                    ->orWhere('bank_reference_number', 'like', '%' . $query . '%')
                    ->orWhere('invoice_number', 'like', '%' . $query . '%')
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', '%' . $query . '%'))
                    ->orWhereHas('bank', fn ($bankQuery) => $bankQuery->where('name', 'like', '%' . $query . '%'));
            });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $payments->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $payments = $payments->paginate(15);

        // $payments = Payment::where('company_id', $companyId)->get();
        return view('transactions.payments.index', compact('payments'));
    }

    public function paymentsCreate($saleId = null)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
   
        $companyId = auth()->user()->company_id;
        $sale = null;
        $payment = new Payment();

        // Initialize variables to store customer and stock details
        $customerId = null;
        $stockIds = [];
        $payableAmount = 0;
        $paidAmount = 0;
        $remainingAmount = 0;
        $invoiceId = null;
        
        $stocks = Stock::where('company_id', $companyId)->get(); // Define $stocks here
        $customers = Customer::where('company_id', $companyId)->get();
        $banks = Bank::where('company_id', $companyId)->get();
        if ($saleId) {
            // Fetch data related to the sale ID, such as customer ID, stock ID, etc.
            $sale = SaleBill::where('company_id', $companyId)->findOrFail($saleId);
            // You can customize this based on your data structure
            $customerId = $sale->customer_id;
            $stockIds = $sale->items()->pluck('stock_id')->toArray();

            // Calculate payable, paid, and remaining amounts based on sale details
            // $payableAmount = $sale->payments()->sum('paid_amount');
            $payableAmount = number_format($sale->items()->sum('totalprice'),2);
            $paidAmount = number_format($sale->items()->sum('totalprice'),2);
            $remainingAmount = 0.00;
            $invoiceId = $saleId;
            
            // return view('transactions.payments.create', compact('customerId', 'stockIds', 'stocks', 'payment', 'customers', 'banks', 'saleId', 'payableAmount', 'paidAmount', 'remainingAmount','invoiceId'));
        }
        return view('transactions.payments.create', compact('customerId', 'stockIds', 'stocks', 'payment', 'customers', 'banks', 'saleId', 'payableAmount', 'paidAmount', 'remainingAmount','invoiceId'));
    }    
    
    public function paymentsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stock_id' => 'required|exists:stocks,id',
            'sales_id' => 'nullable|exists:sales,id',
            'payable_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            // 'remaining_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'payment_type' => 'required|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_reference_number' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_id' => 'nullable|numeric',
            // 'payment_verified_by_cfo' => 'nullable|boolean',
            'remark' => 'nullable|string',
        ]);
    
         // Set the company ID
        $companyId = auth()->user()->company_id;
        $validatedData['company_id'] = $companyId;

        // Calculate the remaining amount
        $remainingAmount = $validatedData['payable_amount'] - $validatedData['paid_amount'];
        
        // Add the remaining amount to the validated data
        $validatedData['remaining_amount'] = $remainingAmount;
        
        // Set the payment verification status
        $validatedData['payment_verified_by_cfo'] = $request->has('payment_verified_by_cfo') ? 1 : 0;
        Payment::create($validatedData);
    
        return redirect()->route('payments.index')->with('success', 'Payment created successfully.');
    }
    

    public function paymentsEdit(Payment $payment)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
        $customers = Customer::where('company_id', $companyId)->get();
        $banks = Bank::where('company_id', $companyId)->get();
        $stocks = Stock::where('company_id', $companyId)->get();
        return view('transactions.payments.edit', compact('payment', 'stocks','customers','banks'));
    }

    public function paymentsUpdate(Request $request, Payment $payment)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stock_id' => 'required|exists:stocks,id',
            'payable_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            // 'remaining_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'payment_type' => 'required|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_reference_number' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_id' => 'nullable|numeric',
            // 'payment_verified_by_cfo' => 'nullable|boolean',
            'remark' => 'nullable|string',
        ]);
    
         // Set the company ID
         $companyId = auth()->user()->company_id;
         $validatedData['company_id'] = $companyId;
 
         // Calculate the remaining amount
         $remainingAmount = $validatedData['payable_amount'] - $validatedData['paid_amount'];
         
         // Add the remaining amount to the validated data
         $validatedData['remaining_amount'] = $remainingAmount;
         
        // Set the payment verification status
        $validatedData['payment_verified_by_cfo'] = $request->has('payment_verified_by_cfo') ? 1 : 0;

        $payment->update($validatedData);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function paymentsDestroy(Payment $payment)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
        $payment->where('company_id', $companyId)->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function getStockDetails(Request $request, Stock $stock)
    {
        // Fetch the stock details
        $stock = Stock::findOrFail($request->stock);
        
        // Return the stock details as JSON response
        return response()->json(['amount' => $stock->amount]);
    }
}
