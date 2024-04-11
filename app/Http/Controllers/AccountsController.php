<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\GeneralLedger;
use App\Models\ChartOfAccount;
use App\Models\AccountsReceivable;
use App\Models\AccountsPayable;
use App\Models\AccountsCategory;

use App\Models\BankTransaction;
use App\Models\Reconcilliation;

use App\Imports\ChartImportClass;
use App\Imports\BankImportClass;

use Maatwebsite\Excel\Facades\Excel;
// use Spatie\SimpleExcel\SimpleExcelReader;
// use Excel;


class AccountsController extends Controller
{
    public function dashboard()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        // Get real-time values for financial metrics
        $income = $this->getIncome();
        $expenses = $this->getExpenses();
        $cashFlow = $income - $expenses;
        $accountBalances = $this->getAccountBalances();
    
        // Get any relevant alerts
        $alerts = $this->getAlerts();
    
        // Pass data to the dashboard view
        return view('accounts.dashboard', [
            'income' => $income,
            'expenses' => $expenses,
            'cashFlow' => $cashFlow,
            'accountBalances' => $accountBalances,
            'alerts' => $alerts,
        ]);
        // return view('accounts.dashboard');
    }
    
    // Method to get income (example, replace with actual logic)
    private function getIncome()
    {
        // Implement logic to calculate income
        return 50000; // Example value
    }
    
    // Method to get expenses (example, replace with actual logic)
    private function getExpenses()
    {
        // Implement logic to calculate expenses
        return 30000; // Example value
    }
    
    // Method to get account balances (example, replace with actual logic)
    private function getAccountBalances()
    {
        // Implement logic to fetch account balances
        return [
            'assets' => 70000,
            'liabilities' => 20000,
            'equity' => 50000,
        ];
    }
    
    // Method to get alerts (example, replace with actual logic)
    private function getAlerts()
    {
        // Implement logic to fetch relevant alerts
        return [
            'Reminder: Quarterly tax filings are due next week.',
            'Alert: Accounts receivable aging report indicates overdue payments.',
        ];
    }
    
    // Ledger Module
    public function ledgerIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $ledgerEntries = GeneralLedger::where('company_id', $companyId)->get();
        return view('accounts.ledgers.index', compact('ledgerEntries'));
    }

    public function ledgerCreate()
    {
        return view('ledger.create');
    }

    public function ledgerStore(Request $request)
    {
        // Validate request
        $request->validate([
            // Define validation rules here
        ]);

        // Create new ledger entry
        Ledger::create([
            // Assign request data to ledger fields
        ]);

        return redirect()->route('ledger.index')->with('success', 'Ledger entry created successfully.');
    }

    public function ledgerEdit($id)
    {
        $ledgerEntry = Ledger::findOrFail($id);
        return view('ledger.edit', compact('ledgerEntry'));
    }

    public function ledgerUpdate(Request $request, $id)
    {
        // Validate request
        $request->validate([
            // Define validation rules here
        ]);

        // Update ledger entry
        $ledgerEntry = Ledger::findOrFail($id);
        $ledgerEntry->update([
            // Assign request data to ledger fields
        ]);

        return redirect()->route('ledger.index')->with('success', 'Ledger entry updated successfully.');
    }

    public function ledgerDestroy($id)
    {
        $ledgerEntry = Ledger::findOrFail($id);
        $ledgerEntry->delete();
        return redirect()->route('ledger.index')->with('success', 'Ledger entry deleted successfully.');
    }

    // Transactions Module
    public function transactionsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $transactions = Transaction::query();

        if ($query) {
            $transactions->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $transactions->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $transactions = $transactions->paginate(15);

        return view('accounts.transactions.index', compact('transactions'));
    }

    public function transactionsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transactions.create', compact('accounts'));
    }

    public function transactionsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Transaction::create($validatedData);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function transactionsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $transaction = Transaction::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transactions.edit', compact('transaction','accounts'));
    }

    public function transactionsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    // dd($transaction);
        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $transaction = Transaction::findOrFail($id);
        $transaction->update($validatedData);
    
        // Redirect back to the index page with a success message
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }
    
    public function transactionsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the transaction and ensure it belongs to the authenticated user's company
        $transaction = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the transaction
        $transaction->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
    

    //Deposits Module
    public function depositsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $deposits = Deposit::query();

        if ($query) {
            $deposits->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $deposits->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $deposits = $deposits->paginate(15);

        return view('accounts.deposits.index', compact('deposits'));
    }

    public function depositsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.deposits.create', compact('accounts'));
    }

    public function depositsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Deposit::create($validatedData);

        return redirect()->route('deposits.index')->with('success', 'deposit created successfully.');
    }

    public function depositsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $deposit = Deposit::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.deposits.edit', compact('deposit','accounts'));
    }

    public function depositsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    // dd($deposit);
        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $deposit = Deposit::findOrFail($id);
        $deposit->update($validatedData);
    
        // Redirect back to the index page with a success message
        return redirect()->route('deposits.index')->with('success', 'deposit updated successfully.');
    }
    
    public function depositsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the deposit and ensure it belongs to the authenticated user's company
        $deposit = Deposit::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the deposit
        $deposit->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('deposits.index')->with('success', 'deposit deleted successfully.');
    }
    

    //Withdrawa Module

    public function withdrawalsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $withdrawals = Withdrawal::query();

        if ($query) {
            $withdrawals->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $withdrawals->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $withdrawals = $withdrawals->paginate(15);

        return view('accounts.withdrawals.index', compact('withdrawals'));
    }

    public function withdrawalsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.withdrawals.create', compact('accounts'));
    }

    public function withdrawalsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Withdrawal::create($validatedData);

        return redirect()->route('withdrawals.index')->with('success', 'withdrawal created successfully.');
    }

    public function withdrawalsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $withdrawal = Withdrawal::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.withdrawals.edit', compact('withdrawal','accounts'));
    }

    public function withdrawalsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    // dd($withdrawal);
        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->update($validatedData);
    
        // Redirect back to the index page with a success message
        return redirect()->route('withdrawals.index')->with('success', 'withdrawal updated successfully.');
    }
    
    public function withdrawalsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the withdrawal and ensure it belongs to the authenticated user's company
        $withdrawal = Withdrawal::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the withdrawal
        $withdrawal->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('withdrawals.index')->with('success', 'withdrawal deleted successfully.');
    }

    //Transfer Module
    public function transfersIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $transfers = Transfer::query();

        if ($query) {
            $transfers->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $transfers->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $transfers = $transfers->paginate(15);

        return view('accounts.transfers.index', compact('transfers'));
    }

    public function transfersCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transfers.create', compact('accounts'));
    }

    public function transfersStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Transfer::create($validatedData);

        return redirect()->route('transfers.index')->with('success', 'transfer created successfully.');
    }

    public function transfersEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // where('company_id', $companyId)->

        $transfer = Transfer::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transfers.edit', compact('transfer','accounts'));
    }

    public function transfersUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    // dd($transfer);
        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $transfer = Transfer::findOrFail($id);
        $transfer->update($validatedData);
    
        // Redirect back to the index page with a success message
        return redirect()->route('transfers.index')->with('success', 'transfer updated successfully.');
    }
    
    public function transfersDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the transfer and ensure it belongs to the authenticated user's company
        $transfer = Transfer::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the transfer
        $transfer->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('transfers.index')->with('success', 'transfer deleted successfully.');
    }


    // Accounts Receivable Module
    public function accountsReceivableIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        
        $receivables = AccountsReceivable::where('company_id', $companyId)->get();
        return view('accounts_receivable.index', compact('receivables'));
    }

    // Implement other functions for accounts receivable module (create, store, edit, update, destroy)

    // Accounts Payable Module
    public function accountsPayableIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        
        $payables = AccountsPayable::where('company_id', $companyId)->get();
        return view('accounts_payable.index', compact('payables'));
    }


    //Chart of Accounts
    public function createChartOfAccount()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        
        // $chartOfAccounts = ChartOfAccount::all();
        $categories = AccountsCategory::orderBy('category')->get();

        return view('accounts.chart_of_accounts.create', compact( 'categories' ) );
    }

    
    public function storeChartOfAccount(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        
        // Validate the request data
        $request->validate([
            'description' => 'required',
            'code' => 'required',
            'type' => 'required',
            'category' => 'required',
            'name' => 'required',
            // Add more validation rules as needed
        ]);

        // Create a new chart of account record in the database
        ChartOfAccount::create([
            'description' => $request->description,
            'code' => $request->code,
            'type' => $request->type,
            'category' => $request->category,
            'name' => $request->name,
            'company_id' => $companyId,
        ]);

        // Redirect back to the chart of accounts page with a success message
        return redirect()->route('chartOfAccounts')->with('success', 'Chart of account created successfully.');
    }

    public function editChartOfAccount($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
         
        $chartOfAccount = ChartOfAccount::where('company_id', $companyId)->find($id);
        $categories = AccountsCategory::orderBy('category')->get();

        return view('accounts.chart_of_accounts.edit', compact( 'chartOfAccount','categories')) ;
    }

    public function updateChartOfAccount(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Validate the request data
        $request->validate([
            'description' => 'required',
            'code' => 'required',
            'type' => 'required',
            'category' => 'required',
            'name' => 'required',
            // Add more validation rules as needed
        ]);

        // Find the chart of account by ID
        $chartOfAccount = ChartOfAccount::where('company_id', $companyId)->findOrFail($id);

        // Update the chart of account with the new data
        $chartOfAccount->update([
            'description' => $request->description,
            'code' => $request->code,
            'type' => $request->type,
            'category' => $request->category,
            'name' => $request->name,
            // Update other fields as needed
        ]);

        // Redirect back to the chart of accounts page with a success message
        return redirect()->route('chartOfAccounts')->with('success', 'Chart of account updated successfully.');
    }

    public function chartOfAccounts(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $chartOfAccounts = ChartOfAccount::query();

        if ($query) {
            $chartOfAccounts->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', '%' . $query . '%')
                        ->orWhere('code', 'like', '%' . $query . '%')
                        ->orWhere('category', 'like', '%' . $query . '%')
                        ->orWhereHas('types', function ($typeQuery) use ($query) {
                            $typeQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $chartOfAccounts->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $chartOfAccounts = $chartOfAccounts->paginate(15);

        // $chartOfAccounts = ChartOfAccount::where('company_id', $companyId)->get();
        return view('accounts.chart_of_accounts.index', compact('chartOfAccounts'));
    }

    public function uploadChartOfAccounts(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // $supplier->where('company_id', $companyId)->delete();
        
        // dd($request->file('file'));

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv, CSV',
        ]);

        // Get the uploaded file
        $file = $request->file('file');
 
        // Process the Excel file
        Excel::import(new ChartImportClass, $file);
 
        return redirect()->back()->with('success', 'File uploaded and data imported successfully.');
    }
        
    public function deleteChartOfAccount($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Find the chart of account by its ID
        $chartOfAccount = ChartOfAccount::findOrFail($id);
        
        // Check if the chart of account belongs to the authenticated user's company
        if ($chartOfAccount->company_id !== $companyId) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        // Delete the chart of account from the database
        $chartOfAccount->delete();
        
        return redirect()->route('chartOfAccounts')->with('success', 'Chart of account deleted successfully.');
    }

    public function indexBankFeeds()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;        
        // $bankTransactions = BankTransaction::where('user_id', auth()->id())->latest()->get();
        $bankFeeds = BankTransaction::where('company_id', $companyId)->latest()->paginate(10);
        return view('accounts.banking.index', compact('bankFeeds'));
    }

    public function uploadBankFeeds(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // $supplier->where('company_id', $companyId)->delete();

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,pdf|max:2048', // Validate file type and size
        ]);

        if ($request->file('file')->isValid()) {
            // $filePath = $request->file('file')->store('bank-feeds'); // Store the uploaded file
            try {
        
                // Get the uploaded file
                $file = $request->file('file');
        
                // Process the Excel file
                Excel::import(new ChartImportClass, $file);

                return redirect()->route('accounts.banking.index')->with('success', 'Bank feeds uploaded successfully.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Failed to process the uploaded file.');
            }

            
        }

        return redirect()->back()->with('error', 'Invalid file uploaded.');
    }

    public function Reconsindex()
    {
        $companyId = auth()->user()->company_id;

        // Retrieve transactions from various tables based on company_id
        // $invoices = Invoice::where('company_id', $companyId)->get();
        // $payments = Payment::where('company_id', $companyId)->get();
        $deposits = Deposit::where('company_id', $companyId)->get();
        $withdrawals = Withdrawal::where('company_id', $companyId)->get();
        $transfers = Transfer::where('company_id', $companyId)->get();
        $transactions = Transaction::where('company_id', $companyId)->get();
        // Include other relevant tables

        return view('accounts.banking.reconcilliation', compact('withdrawals', 'transfers', 'deposits','transactions'));
    }

    public function matchTransactions(Request $request)
    {
        // Retrieve data from the form submission
        $bankTransactionId = $request->input('bank_transaction');
        $accountingTransactionId = $request->input('account_transaction');

        // Fetch the selected bank transaction and accounting transaction from the database
        $bankTransaction = BankTransaction::findOrFail($bankTransactionId);
        $accountingTransaction = Transaction::findOrFail($accountingTransactionId);

        // Perform matching logic here
        // Example: Compare amount, date, and description to determine if they match
        $matchCriteria = $this->compareTransactions($bankTransaction, $accountingTransaction);

        // Check if the transactions match
        if ($matchCriteria) {
            // For demonstration purposes, let's assume we mark the accounting transaction as matched with the bank feed
            $accountingTransaction->matched_with_bank_feed = true;
            $accountingTransaction->save();

            // Redirect back to the reconciliation page with a success message
            return redirect()->route('reconciliation.index')->with('success', 'Transactions matched successfully.');
        } else {
            // Redirect back to the reconciliation page with a warning message
            return redirect()->route('reconciliation.index')->with('warning', 'Transactions do not match.');
        }
    }

    private function compareTransactions($bankTransaction, $accountingTransaction) {
        // Implement your matching logic here
        // Example: Compare amount, date, and description
        if ($bankTransaction->amount === $accountingTransaction->amount &&
            $bankTransaction->date === $accountingTransaction->date &&
            $bankTransaction->description === $accountingTransaction->description) {
            return true;
        } else {
            return false;
        }
    }
}
