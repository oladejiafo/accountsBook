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
use App\Models\ReturnedProduct;
use App\Models\ReturnedProduct as ReturnModel;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionAccountMapping;
use App\Models\ChartOfAccount;

use App\Notifications\ReturnProcessedNotification;  
use App\Notifications\ReturnApprovalNotification;

// use App\Mail\ReturnApprovalNotification;
// use App\Mail\ReturnProcessedNotification;

use App\Http\Requests\SaleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    //SALES
    public function salesIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
        $perPage = $request->input('per_page', 25);
        $sales = SaleBill::where('company_id', $companyId)->with('saleItems')->orderBy('created_at', 'desc')->paginate($perPage);

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
            ->where('reference_number', $sale->id)
            ->get();

        // Loop through the existing entries
        foreach ($existingEntries as $existingEntry) {
            // Check if the existing entry corresponds to any updated sales mapping
            $matchingMapping = $updatedSalesMapping->first(function ($mapping) use ($existingEntry) {
                return $mapping->debit_account_id === $existingEntry->account_id || $mapping->credit_account_id === $existingEntry->account_id;
            });

            if ($matchingMapping) {
                // Retrieve the corresponding updated mapping
                $updatedMapping = $updatedSalesMapping->where('id', $matchingMapping->id)->first();

                // Retrieve the corresponding accounts
                $debitAccount = $accounts[$updatedMapping->debit_account_id] ?? null;
                $creditAccount = $accounts[$updatedMapping->credit_account_id] ?? null;

                // Update the existing entry with the updated information
                $existingEntry->update([
                'amount' => $saleBillDetails->total,
                'type' => $debitAccount ? $debitAccount->type : null,
                'date' => date('Y-m-d'),
                'description' => "Updated sales transaction from customer"
                ]);


                // Remove the updated mapping from the list to avoid duplicating it in the creation step
                $updatedSalesMapping = $updatedSalesMapping->reject(function ($mapping) use ($updatedMapping) {
                    return $mapping->id === $updatedMapping->id;
                });
            } else {
                // If there is no matching mapping, delete the existing entry
                $existingEntry->delete();
            }
        }

        // Create new entries for any remaining mappings
        foreach ($updatedSalesMapping as $mapping) {
            // Retrieve the accounts associated with the updated mapping
            $debitAccount = $accounts[$mapping->debit_account_id] ?? null;
            $creditAccount = $accounts[$mapping->credit_account_id] ?? null;

            if ($debitAccount && $creditAccount) {
                // Create new entries for the updated mappings
                Transaction::create([
                    'reference_number' => $sale->id,
                    'account_id' => $debitAccount->id,
                    'amount' => $saleBillDetails->total,
                    'type' => $debitAccount->type,
                    // Add other fields as needed
                    'date' => date('Y-m-d'),
                    'company_id' => $companyId,
                    'name' => "Sales",
                    'description' => "Sales transaction from customer",
                ]);

                Transaction::create([
                    'reference_number' => $sale->id,
                    'account_id' => $creditAccount->id,
                    'amount' => $saleBillDetails->total,
                    'type' => $creditAccount->type,
                    // Add other fields as needed
                    'date' => date('Y-m-d'),
                    'company_id' => $companyId,
                    'name' => "Sales",
                    'description' => "Sales transaction from customer",
                ]);
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
       
        // // Retrieve all return transactions
        // $returnTransactions = ReturnTransaction::where('company_id', $companyId)->get();
    
        $customers = Customer::where('company_id', $companyId)->get(); 

        return view('transactions.returns.create', compact('customers'));
        // return view('transactions.returns.show', compact('returnTransactions'));
    }
    
    public function processReturn(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items.*.product_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'nullable',
            'items.*.condition' => 'required',
            'items.*.reason' => 'required',
        ]);

        $returnTransaction = ReturnTransaction::create([
            'customer_id' => $request->customer_id,
            'company_id' => $companyId,
            'return_status' => 'pending', 
            'reason_for_return' => $request->reason,
            'refund_amount' => $request->price,
            'approval_required' => $request->has('approval_required'),
            'return_date' => $request->return_date,
        ]);
        
        // Associate return products with the return transaction
        foreach ($request->items as $item) {

            $product = Stock::find($item['product_id']); 

            if ($product) {
                $productName = $product->name; 
            } else {
                continue; 
            }

            ReturnedProduct::create([
                'return_id' => $returnTransaction->id,
                'company_id' => $companyId,
                'product_id' => $item['product_id'],
                'product_name' => $productName,
                'quantity' => $item['quantity'],
                'condition' => $item['condition'],
                // 'reason' => $item['reason'],
            ]);

            // Update stock quantity
            $stock = Stock::where('company_id', $companyId)->find($item['product_id']);
            if ($stock) {
                $stock->quantity += $item['quantity'];
                $stock->save();
            }
        }
            
        // Approval logic goes here
        if ($request->has('approval_required')) {
            // Update return transaction status to pending approval
            $returnTransaction->update([
                'approval_status' => 'Pending',
            ]);
    
            $approversEmails = User::where('company_id', $companyId)->where('role', 'approver')->pluck('email')->toArray();
            \Mail::to($approversEmails)->send(new ReturnApprovalNotification($returnTransaction));
    
            return redirect()->back()->with('success', 'Return request submitted for approval.');
        } else {
            $totalRefundAmount = 0;

            if ($returnTransaction->returnProducts && $returnTransaction->returnProducts->count() > 0) {
                foreach ($returnTransaction->returnProducts as $returnProduct) {
                    $refundAmountForProduct = $returnProduct->quantity * $returnProduct->unit_price;
                    $totalRefundAmount += $refundAmountForProduct;
                }
            }
            // Update customer balance (if applicable)
            $customer = $returnTransaction->customer;
            if ($customer) {
                $customer->balance += $totalRefundAmount;
                $customer->save();
            }

            // Update return status
            $returnTransaction->update([
                'return_status' => 'Completed',
                'notes' => 'Refund processed successfully', 
            ]);
    
            // Notify approvers
            $approversEmails = User::where('company_id', $companyId)->where('role', 'approver')->pluck('email')->toArray();
            \Mail::to($approversEmails)->send(new ReturnProcessedNotification($returnTransaction));
    
            // Notify the customer
            $customerEmail = $returnTransaction->customer->email;
            \Mail::to($customerEmail)->send(new ReturnProcessedNotification($returnTransaction));
    
            // Reverse sales transactions
            if ($returnTransaction->returnProducts) {
            foreach ($returnTransaction->returnProducts as $returnProduct) {
                 // Find the original sales transaction
                $originalSalesTransaction = SaleItem::where([
                    'stock_id' => $returnProduct->product_id,
                    'company_id' => $companyId,
                ])->first();

                if ($originalSalesTransaction) {
                    // Reverse the sales transaction by updating the quantities and amounts
                    $originalSalesTransaction->update([
                        'quantity' => $originalSalesTransaction->quantity - $returnProduct->quantity,
                        'totalprice' => $originalSalesTransaction->totalprice - ($returnProduct->unit_price * $returnProduct->quantity),
                    ]);
        
                    // Debit the sales account and credit the revenue account (or vice versa depending on your accounting setup)
                    $debitTransaction = new Transaction();
                    $debitTransaction->reference_number = $returnTransaction->id;
                    $debitTransaction->account_id = $originalSalesTransaction->sale_bill_id; 
                    $debitTransaction->amount = $returnProduct->unit_price * $returnProduct->quantity;
                    $debitTransaction->type = 'debit'; 
                    $debitTransaction->date = now();
                    $debitTransaction->company_id = $returnTransaction->company_id;
                    $debitTransaction->name = "Sales Return";
                    $debitTransaction->description = "Reverse sales transaction due to return";
                    $debitTransaction->save();
        
                    $creditTransaction = new Transaction();
                    $creditTransaction->reference_number = $returnTransaction->id;
                    $creditTransaction->account_id = $originalSalesTransaction->sale_bill_id; 
                    $creditTransaction->amount = $returnProduct->unit_price * $returnProduct->quantity;
                    $creditTransaction->type = 'credit'; 
                    $creditTransaction->date = now();
                    $creditTransaction->company_id = $returnTransaction->company_id;
                    $creditTransaction->name = "Sales Return";
                    $creditTransaction->description = "Reverse sales transaction due to return";
                    $creditTransaction->save();
                }
           
            }
            }
    
            // Auto Reverse payments (if applicable)
            if ($returnTransaction->payment_method === 'Credit Card') {
                $refundResponse = $this->processCreditCardRefund($returnTransaction, $totalRefundAmount);
                if ($refundResponse->success) {
                    // Payment reversal successful
                    // You may update the refund transaction status or log refund details
                } else {
                    // Payment reversal failed
                    // Handle error scenario, log error, display message, etc.
                    return redirect()->back()->with('error', 'Credit card refund failed.');
                }
            } elseif ($returnTransaction->payment_method === 'Cash') {
                // For cash refunds, update refund transaction status and log refund details
                $this->processCashRefund($returnTransaction, $totalRefundAmount);
            } elseif ($returnTransaction->payment_method === 'Bank Transfer') {
                // For bank transfer refunds, update refund transaction status and log refund details
                $this->processBankTransferRefund($returnTransaction, $totalRefundAmount);
            } else {
                // Handle other payment methods (e.g., store credit)
                // Update refund transaction status or log refund details as necessary
                $this->processOtherPaymentRefund($returnTransaction, $totalRefundAmount);
            }

            // Update financial reporting (if applicable)
            // Code to update financial reporting goes here
    
            return redirect()->back()->with('success', 'Return processed successfully.');
        }
    }

    private function processCreditCardRefund($returnTransaction, $totalRefundAmount)
    {
        // // Implement your credit card refund logic here
        // $paymentGateway = new PaymentGateway(); // Hypothetical payment gateway class
        // $response = $paymentGateway->refund([
        //     'transaction_id' => $returnTransaction->transaction_id, // ID of the original transaction
        //     'amount' => $totalRefundAmount,
        // ]);
    
        // return (object)['success' => $response->isSuccessful(), 'message' => $response->getMessage()];
    }
    
    private function processCashRefund($returnTransaction, $totalRefundAmount)
    {
        // // Update necessary records and log details
        // RefundTransaction::create([
        //     'return_transaction_id' => $returnTransaction->id,
        //     'amount' => $totalRefundAmount,
        //     'payment_method' => 'Cash',
        //     'transaction_id' => 'CASH_REFUND_' . $returnTransaction->id,
        //     'refund_date' => now(),
        //     'notes' => 'Cash refund processed successfully',
        //     'company_id' => $returnTransaction->company_id,
        // ]);
    
        // return (object)['success' => true, 'message' => 'Cash refund processed successfully.'];
    }
    

    private function processBankTransferRefund($returnTransaction, $totalRefundAmount)
    {
        // // Implement your bank transfer refund logic here
        // // This could be an API call to your bank or a manual process
        // // For example purposes, we'll assume a manual process
    
        // RefundTransaction::create([
        //     'return_transaction_id' => $returnTransaction->id,
        //     'amount' => $totalRefundAmount,
        //     'payment_method' => 'Bank Transfer',
        //     'transaction_id' => 'BANK_TRANSFER_REFUND_' . $returnTransaction->id,
        //     'refund_date' => now(),
        //     'notes' => 'Bank transfer refund processed successfully',
        //     'company_id' => $returnTransaction->company_id,
        // ]);
    
        // return (object)['success' => true, 'message' => 'Bank transfer refund processed successfully.'];
    }
    
    private function processOtherPaymentRefund($returnTransaction, $totalRefundAmount)
    {
        // // Update customer's store credit balance
        // $customer = $returnTransaction->customer;
        // $customer->store_credit += $totalRefundAmount;
        // $customer->save();
    
        // RefundTransaction::create([
        //     'return_transaction_id' => $returnTransaction->id,
        //     'amount' => $totalRefundAmount,
        //     'payment_method' => 'Store Credit',
        //     'transaction_id' => 'STORE_CREDIT_REFUND_' . $returnTransaction->id,
        //     'refund_date' => now(),
        //     'notes' => 'Store credit refund processed successfully',
        //     'company_id' => $returnTransaction->company_id,
        // ]);
    
        // return (object)['success' => true, 'message' => 'Store credit refund processed successfully.'];
    }    

    public function fetchCustomerTransactions(Request $request)
    { 
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return response()->json(['error' => 'Unauthorized access.'], 401);
        }

        $companyId = auth()->user()->company_id;

        $customerID = $request->input('customerID');
        dd($customerID);

        if (isset($customerID)) {

            $transactions = Transaction::where('customer_id', $customerID)
                                ->orderBy('created_at', 'desc')
                                ->get();

            // Prepare the transactions data to be returned
            // $formattedTransactions = $transactions->map(function ($transaction) {
            //     return [
            //         'id' => $transaction->id,
            //         'date' => $transaction->created_at->format('m-d-Y'), 
            //         'description' => $transaction->description, 
            //     ];
            // });

            return response()->json(['transactions' => $transactions]);
            $responseHTML = view('partials.transactions_table', compact('transactions'))->render();
    
            return response()->json([
                'html' => $responseHTML
            ]);
                        // return response()->json(['customers' => $customers, 'transactions' => $formattedTransactions]);
        } else {
            // If no customers are found, return an error response
            return response()->json(['error' => 'No matching customers found'], 404);
        }
    }

    public function returnsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
     
        $returnsQuery = ReturnTransaction::where('company_id', $companyId);

        // Apply search filter if search query is present
        if ($request->has('search')) {
            $search = $request->query('search');
            $returnsQuery->where(function($query) use ($search) {
                $query->whereHas('customer', function($subquery) use ($search) {
                    $subquery->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%")
                            ->orWhere('phone', 'like', "%$search%");
                });
            });
        }
        // Paginate customers based on the per_page query parameter or default
        $perPage = $request->query('per_page', 25);
        $customers = $returnsQuery->paginate($perPage);

        $products = Stock::where('company_id', $companyId)->get();

        return view('transactions.returns.index', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function getProductsByCustomer($customerId)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
     
        $products = SaleItem::join('sale_bills', 'sale_items.sale_bill_id', '=', 'sale_bills.id')
                            ->join('stocks', 'sale_items.stock_id', '=', 'stocks.id')
                            ->where('sale_bills.customer_id', $customerId)
                            ->where('stocks.company_id', $companyId)
                            ->select('stocks.id', 'stocks.name','sale_items.quantity', 'sale_items.totalprice')
                            ->distinct()
                            ->get();

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->quantity,
                'totalprice' => $product->totalprice,
            ];
        });
                                    
        return response()->json([
            'products' => $formattedProducts,
        ]);
    }

    public function returnsEdit($id)
    {
        // Check if user is authenticated and has a company ID
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        // Fetch the return transaction by ID and ensure it belongs to the user's company
        $return = ReturnTransaction::where('id', $id)
                        ->where('company_id', auth()->user()->company_id)
                        ->first();
    
        // If return transaction not found or doesn't belong to the company, redirect with error
        if (!$return) {
            return redirect()->route('returns.index')->with('error', 'Return not found or unauthorized access.');
        }
      // Fetch all customers belonging to the user's company
      $customers = Customer::where('company_id', auth()->user()->company_id)->get();
      $products = Stock::where('company_id', auth()->user()->company_id)->get();
        // Load the edit view with the return transaction data
        return view('transactions.returns.edit', compact('return', 'customers','products'));
    }
    

    public function returnsUpdate(Request $request, $id)
    { 
        // Validate incoming data
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'reason' => 'required|string',
            'price' => 'required|numeric|min:0',
            'reason_for_return' => 'nullable|string',
            'refund_amount' => 'nullable|numeric|min:0',

            'items.*.product_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|string',
            'items.*.product_name' => 'required|string',
        ]);
    
        // Check if user is authenticated and has a company ID
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $return = ReturnTransaction::where('id', $id)
        ->where('company_id', auth()->user()->company_id)
        ->first();
        // Check if the return belongs to the user's company
        if (!$return) {
            return redirect()->route('returns.index')->with('error', 'Return not found or unauthorized access.');
        }
    

        // Update return details
        $return->customer_id = $request->customer_id;
        $return->return_date = $request->return_date;
        $return->reason_for_return = $request->reason;
        $return->refund_amount = $request->price;
        // $return->approval_required = $request->has('approval_required');
        $return->save();
    
        // Update or create associated returned products
        foreach ($request->items as $item) {
            // Update or create returned product
            $returnedProduct = ReturnedProduct::updateOrCreate(
                [
                    'return_id' => $return->id,
                    'product_id' => $item['product_id'],
                ],
                [
                    'company_id' => auth()->user()->company_id,
                    'product_name' => $item['product_name'], 
                    'quantity' => $item['quantity'],
                    // 'total_price' => $item['price'], 
                    'condition' => $item['condition'],
                    // 'reason' => $item['reason'],
                ]
            );

            // Update stock quantity (if needed)
            $stock = Stock::where('company_id', auth()->user()->company_id)
                        ->where('id', $item['product_id'])
                        ->first();

            if ($stock) {
                // Calculate new stock quantity after return update
                $newQuantity = $stock->quantity + $item['quantity'];
                $stock->update(['quantity' => $newQuantity]);
            }
        }
        return redirect()->route('returns.index')->with('success', 'Return updated successfully.');
    }
    

    public function returnsDestroy(ReturnModel $return)
    {
        // Check if user is authenticated and has a company ID
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        // Check if the return belongs to the user's company
        if ($return->company_id !== auth()->user()->company_id) {
            return redirect()->route('returns.index')->with('error', 'Unauthorized access.');
        }
    
        $return->delete();
        return redirect()->route('returns.index')->with('success', 'Return deleted successfully.');
    }
    

    public function returnCustomers(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = auth()->user()->company_id;

        $term = $request->input('name');

        // Query the database to find customers whose names start with the given term
        $customers = Customer::where('company_id', $companyId)->where('name', 'like', $term . '%')->limit(10)->get();
    
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
    
    public function purchasesIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $perPage = $request->input('per_page', 25);
        $purchases = PurchaseBill::where('company_id', $companyId)->with('items')->orderBy('created_at', 'desc')->paginate($perPage);
       
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

    public function supplierIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $perPage = $request->input('per_page', 25);
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('created_at', 'desc')->paginate($perPage);
       
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
        $purchases = $supplier->purchases()->paginate(25);
    
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
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        //where('company_id', $companyId)->
        $customerName = $request->get('name');
        $customers = Customer::where('company_id', $companyId)->where('name', 'like', '%' . $customerName . '%')->get();
    
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
        $perPage = $request->input('per_page', 25);
        $customers = $customers->paginate($perPage);        

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
        $sales = $customer->sales()->paginate(25);
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
        $perPage = $request->input('per_page', 25);
        $payments = $payments->paginate($perPage);

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
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
        // Fetch the stock details
        $stock = Stock::where('company_id', $companyId)->findOrFail($request->stock);
        
        // Return the stock details as JSON response
        return response()->json(['amount' => $stock->amount]);
    }
}
