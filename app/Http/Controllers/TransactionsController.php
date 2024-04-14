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

use App\Notifications\ReturnProcessedNotification;  
use App\Notifications\ReturnApprovalNotification;

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
    
    public function showReturnsForm()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;
       //where('company_id', $companyId)->
       
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
        //where('company_id', $companyId)->
        
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
        ]);
    
        // Create a new return transaction record
        $returnTransaction = ReturnTransaction::create([
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
            'company_id' => auth()->user()->company_id, // Assign company_id
            // Assign other return transaction fields from the validated data
        ]);
    
        // Associate return products with the return transaction
        foreach ($validatedData['product_id'] as $key => $productId) {
            ReturnProduct::create([
                'return_transaction_id' => $returnTransaction->id,
                'product_id' => $productId,
                'quantity' => $validatedData['quantity'][$key],
                'condition' => $validatedData['condition'][$key],
                // Assign other return product fields from the validated data
                // For example:
                'product_name' => $validatedData['product_name'][$key],
                'unit_price' => $validatedData['unit_price'][$key],
                'company_id' => $companyId, // Assign company_id
            ]);

            // Update inventory levels
            $product = Stock::where('company_id', $companyId)->findOrFail($productId);
            $product->quantity += $validatedData['quantity'][$key];
            $product->save();
        }


        //$approvalRequired = $request->has('approval_required');

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
                $refundAmountForProduct = $returnProduct->quantity * $returnProduct->product->price;
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
            
            // Add conditions to identify the original sales transaction
            $originalSalesTransaction = $returnProduct->product->saleItems()->where([
                // Assuming 'stock_id' is used to identify the product in the sales transactions
                'stock_id' => $returnProduct->product_id,
                // Add company_id condition
                'company_id' => $companyId,
                // You may need additional conditions based on your specific sales data structure
            ])->first();

            // Update fields to reflect the return, such as quantity or amount
            if ($originalSalesTransaction) {
                // Calculate the returned quantity based on the return product quantity
                $returnedQuantity = $returnProduct->quantity;
                // Calculate the returned total price based on the return product quantity and original unit price
                $returnedTotalPrice = $returnProduct->quantity * $returnProduct->unit_price;

                // Update the original sales transaction fields to reflect the return
                $originalSalesTransaction->update([
                    // Assuming 'quantity' is the field representing the quantity sold in SaleItem model
                    'quantity' => $originalSalesTransaction->quantity - $returnedQuantity,
                    'totalprice' => $originalSalesTransaction->totalprice - $returnedTotalPrice,
                ]);
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

    public function customersIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $customers = Customer::where('company_id', $companyId)->get();
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

    public function paymentsIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $payments = Payment::where('company_id', $companyId)->get();
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
        $stocks = Stock::where('company_id', $companyId)->get(); // Define $stocks here
    
        if ($saleId) {
            // Fetch data related to the sale ID, such as customer ID, stock ID, etc.
            $sale = SaleBill::where('company_id', $companyId)->findOrFail($saleId);
            // You can customize this based on your data structure
            $customerId = $sale->customer_id;
            $stockIds = $sale->items()->pluck('stock_id')->toArray();
    
            // Pass the relevant data to the view
            return view('transactions.payments.create', compact('customerId', 'stockIds', 'stocks','payment'));
        }
    
        return view('transactions.payments.create', compact('stocks', 'payment'));
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
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string',
            'invoice_id' => 'nullable|numeric',
            'recipient_type' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'payment_status' => 'nullable|string|max:255',
        ]);
    
        $companyId = auth()->user()->company_id;
        $validatedData['company_id'] = $companyId;
    
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

        return view('transactions.payments.edit', compact('payment'));
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
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string',
            'invoice_id' => 'nullable|numeric',
            'recipient_type' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'payment_status' => 'nullable|string|max:255',
        ]);
    
        $companyId = auth()->user()->company_id;
        $validatedData['company_id'] = $companyId;

        $payment->update($validatedData);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
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
