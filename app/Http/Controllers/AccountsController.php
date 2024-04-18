<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleBill;
use App\Models\PurchaseBill;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\GeneralLedger;
use App\Models\ChartOfAccount;
use App\Models\AccountsReceivable;
use App\Models\AccountsPayable;
use App\Models\AccountsCategory;
use App\Models\TransactionAccountMapping;
use App\Models\TransactionType;
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

    public function ledgerIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Fetch account categories
        $accountCategories = AccountsCategory::all(); // Assuming AccountCategory is your model for account categories

        // Initialize variables
        $keyword = $request->input('keyword');
        $searchAccount = $request->input('search_account');
        $searchDate = $request->input('search_date');

        // Start building the query
        $transactionsQuery = Transaction::where('company_id', $companyId);

        // Apply filters based on form inputs
        if ($keyword) {
            $transactionsQuery->where(function ($query) use ($keyword) {
                $query->where('type', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('date', 'like', '%' . $keyword . '%');
            });
        }

        if ($searchAccount && $searchAccount != 'all') {
            $transactionsQuery->whereHas('account', function ($accountQuery) use ($searchAccount) {
                // dd($searchAccount);
                $accountQuery->where(function ($query) use ($searchAccount) {
                    $query->where('type', 'like', '%' . $searchAccount . '%')
                          ->orWhere('category', 'like', '%' . $searchAccount . '%');
                });
            });
        }
        
        $tday = date(now());  // Today's Date
        if ($searchDate && $searchDate != 'all') {
            switch ($searchDate) {
                case 'today':
                    $transactionsQuery->where('date', $tday);
                    break;
                case 'last_7_days':
                    $transactionsQuery->whereBetween('date', [now()->subDays(7), now()]);
                    break;
                case 'last_30_days':
                    $transactionsQuery->whereBetween('date', [now()->subDays(30), now()]);
                    break;
                case 'last_60_days':
                    $transactionsQuery->whereBetween('date', [now()->subDays(60), now()]);
                    break;
                case 'last_90_days':
                    $transactionsQuery->whereBetween('date', [now()->subDays(90), now()]);
                    break;
                case 'current_month':
                    $transactionsQuery->whereMonth('date', now()->month);
                    break;
                case 'last_month':
                    $transactionsQuery->whereMonth('date', now()->subMonth()->month);
                    break;
                case 'last_3_months':
                    $transactionsQuery->whereBetween('date', [now()->subMonths(3), now()]);
                    break;
                default:
                    // Retrieve all records
                    break;
            }            
        }

        // Execute the query and get the results
        // $transactions = $transactionsQuery->get();
        $transactions = $transactionsQuery->paginate(30);
        // dd($transactions);
        $totalDebit = 0;
        $totalCredit = 0;

        // Calculate starting balance based on transactions
        $startingBalance = 0;

        foreach ($transactions as $transaction) {
            // Determine transaction type and account category (case-insensitive)
            $transactionType = strtolower($transaction->type);
            $accountCategory = strtolower(optional($transaction->account)->category);

            // Increment total debit and total credit based on transaction type and account category
            if (in_array($transactionType, ['income', 'asset']) || in_array($accountCategory, ['income', 'asset'])) {
                $totalDebit += abs($transaction->amount);
            } elseif (in_array($transactionType, ['expense', 'liability', 'equity']) || in_array($accountCategory, ['expense', 'liability', 'equity'])) {
                $totalCredit += abs($transaction->amount);
            }
        
            // Adjust starting balance based on transaction amount and account category
            if ($transaction->account) {
                if (strtolower($transaction->type) === 'income' || strtolower($transaction->type) === 'equity' || strtolower($transaction->account->category) === 'income' || strtolower($transaction->account->category) === 'equity') {
                    $startingBalance += $transaction->amount;
                } elseif (strtolower($transaction->type) === 'expense' || strtolower($transaction->type) === 'asset' || strtolower($transaction->account->category) === 'expense' || strtolower($transaction->account->category) === 'asset') {
                    $startingBalance -= $transaction->amount;
                } elseif (strtolower($transaction->type) === 'liability' || strtolower($transaction->account->category) === 'liability') {
                    $startingBalance += $transaction->amount;
                }
            }
        }

        // Calculate total adjusted balance
        $totalAdjustedBalance = $startingBalance + $transactions->sum('amount');

        // Return the view with the transactions, account categories, and calculated values
        return view('accounts.ledgers.index', compact('transactions', 'accountCategories', 'totalDebit', 'totalCredit', 'totalAdjustedBalance','startingBalance'));
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

        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('code')->get();
        return view('accounts.transactions.create', compact('accounts'));
    }

    public function getAccountClassifications(Request $request)
    {
        $selectedType = $request->selectedType;
        $companyId = auth()->user()->company_id;
    
        // Query the database to get account classifications based on the selected type
        $accounts = ChartOfAccount::where('company_id', $companyId)
            ->where('type', $selectedType)
            ->orderBy('code')
            ->get();
    
        // Generate options for the account classification dropdown
        $options = '<option value="" disabled selected>Select Account Type</option>';
        foreach ($accounts as $account) {
            $options .= '<option value="'.$account->id.'">'.$account->code. ' - ' .$account->description .'</option>';
        }
    
        // Return options as JSON response
        return response()->json($options);
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
            'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
    
        $transaction = Transaction::where('company_id', $companyId)->findOrFail($id);
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
        $deposits = Transaction::query();

        if ($query) {
            $deposits->where('company_id', $companyId)
                ->where('transaction_name','=', 'Deposit')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $deposits->where('company_id', $companyId)->where('transaction_name','=', 'Deposit');
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
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Deposit';

        Transaction::create($validatedData);

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

        $deposit = Transaction::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.deposits.edit', compact('deposit','accounts'));
    }

    public function depositsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      

        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Deposit';
        
        $deposit = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
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
        $deposit = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the deposit
        $deposit->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('deposits.index')->with('success', 'deposit deleted successfully.');
    }
    

    //Withdrawals Module
    public function withdrawalsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $withdrawals = Transaction::query();

        if ($query) {
            $withdrawals->where('company_id', $companyId)
                ->where('transaction_name','=', 'Withdrawal')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $withdrawals->where('company_id', $companyId)->where('transaction_name','=', 'Withdrawal');
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
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Withdrawal';

        Transaction::create($validatedData);

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

        $withdrawal = Transaction::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.withdrawals.edit', compact('withdrawal','accounts'));
    }

    public function withdrawalsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      

        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Withdrawal';
        
        $withdrawal = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
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
        $withdrawal = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the withdrawal
        $withdrawal->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('withdrawals.index')->with('success', 'withdrawal deleted successfully.');
    }

    //Transfers Module
    public function transfersIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $transfers = Transaction::query();

        if ($query) {
            $transfers->where('company_id', $companyId)
                ->where('transaction_name','=', 'Transfer')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('type', 'like', '%' . $query . '%')
                        ->orWhere('date', 'like', '%' . $query . '%')
                        ->orWhereHas('account', function ($accountQuery) use ($query) {
                            $accountQuery->where('category', 'like', '%' . $query . '%');
                        });
                });
        } else {
            // Continue chaining methods on the $chartOfAccounts query builder instance
            $transfers->where('company_id', $companyId)->where('transaction_name','=', 'Transfer');
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

        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transfers.create', compact('accounts','transactionTypes'));
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
            'to_account_id' => 'nullable',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Transfer';

        Transaction::create($validatedData);

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
        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $transfer = Transaction::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transfers.edit', compact('transfer','accounts','transactionTypes'));
    }

    public function transfersUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      

        // Validate the incoming request data
        $validatedData = $request->validate([
            'type' => 'required',
            'account_id' => 'required',
	    'to_account_id' => 'nullable',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            // 'transaction_name' =>  'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);
    
        // Ensure the company_id is set correctly
        $validatedData['company_id'] = auth()->user()->company_id;
        $validatedData['transaction_name'] = 'Transfer';
        
        $transfer = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
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
        $transfer = Transaction::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
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
            // 'name' => 'required',
            // Add more validation rules as needed
        ]);

        // Create a new chart of account record in the database
        ChartOfAccount::create([
            'description' => $request->description,
            'code' => $request->code,
            'type' => $request->type,
            'category' => $request->category,
            // 'name' => $request->name,
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
            // 'name' => 'required',
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
            // 'name' => $request->name,
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
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Retrieve transactions from various tables based on company_id
        // $invoices = Invoice::where('company_id', $companyId)->get();
        // $payments = Payment::where('company_id', $companyId)->get();
        // $deposits = Deposit::where('company_id', $companyId)->get();
        // $withdrawals = Withdrawal::where('company_id', $companyId)->get();
        // $transfers = Transfer::where('company_id', $companyId)->get();
        $transactions = Transaction::where('company_id', $companyId)->get();

        $bankTransactions = BankTransaction::where('company_id', $companyId)->get();
        // Include other relevant tables
        $accountingTransactions = $transactions->unique();
    
        return view('accounts.banking.reconcilliation', compact('transactions','bankTransactions', 'accountingTransactions'));
    }

    public function matchTransactions(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        // Retrieve data from the form submission
        $bankTransactionId = $request->input('bank_transaction');
        $accountingTransactionId = $request->input('account_transaction');

        // Fetch the selected bank transaction and accounting transaction from the database
        $bankTransaction = BankTransaction::where('company_id', $companyId)->findOrFail($bankTransactionId);
        $accountingTransaction = Transaction::where('company_id', $companyId)->findOrFail($accountingTransactionId);

        // Fetch the accounting transaction from the relevant table based on its type
        switch ($bankTransaction->type) {
            case 'payment':
                $accountingTransaction = Payment::where('company_id', $companyId)->findOrFail($accountingTransactionId);
                break;
            case 'sale':
                $accountingTransaction = SaleBill::where('company_id', $companyId)->findOrFail($accountingTransactionId);
                break;
            case 'purchase':
                $accountingTransaction = PurchaseBill::where('company_id', $companyId)->findOrFail($accountingTransactionId);
                break;
            // Add cases for other types as needed
            default:
                $accountingTransaction = Transaction::where('company_id', $companyId)->findOrFail($accountingTransactionId);
                break;
        }

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

    //Mappings

    public function transactionAccountMappingIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
        
        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $mappings = TransactionAccountMapping::where('company_id', $companyId)->get();
        $chartOfAccounts = ChartOfAccount::where('company_id', $companyId)->get();
        return view('accounts.chart_of_accounts.transaction-account-mapping', compact('mappings','chartOfAccounts','transactionTypes'));
    }

    public function transactionAccountMappingCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
        
        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $mappings = TransactionAccountMapping::where('company_id', $companyId)->get();
        $chartOfAccounts = ChartOfAccount::where('company_id', $companyId)->get();
        return view('accounts.chart_of_accounts.transaction-account-mapping', compact('mappings','chartOfAccounts','transactionTypes'));
    }

    public function transactionAccountMappingStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
        
        // Validate incoming request
        $validatedData = $request->validate([
            'transaction_type' => 'required',
            'account_id' => 'required',
            'is_credit' => 'required',
        ]);
        $validatedData['company_id'] = auth()->user()->company_id;
        // Create new mapping
        TransactionAccountMapping::create($validatedData);

        return redirect()->route('transaction-account-mapping.index')->with('success', 'Mapping created successfully.');
    }

    public function transactionAccountMappingEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $mapped = TransactionAccountMapping::where('company_id', $companyId)->findOrFail($id);
        $mappings = TransactionAccountMapping::where('company_id', $companyId)->get();
        $chartOfAccounts = ChartOfAccount::where('company_id', $companyId)->get();
        return view('accounts.chart_of_accounts.transaction-account-mapping', compact('mappings','chartOfAccounts','transactionTypes','mapped'));
    }

    public function transactionAccountMappingUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

       // Validate incoming request
       $validatedData = $request->validate([
        'transaction_type' => 'required',
        'account_id' => 'required',
        'is_credit' => 'required',
        ]);
        $validatedData['company_id'] = auth()->user()->company_id;
        // Find and update the mapping
        $mapping = TransactionAccountMapping::findOrFail($id);
        $mapping->update($validatedData);

        return redirect()->route('transaction-account-mapping.index')->with('success', 'Mapping updated successfully.');
    }

    public function transactionAccountMappingDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
          // Find and delete the mapping
          $mapping = TransactionAccountMapping::where('company_id', $companyId)->findOrFail($id);
          $mapping->delete();
  
          return redirect()->route('transaction-account-mapping.index')->with('success', 'Mapping deleted successfully.');
    }
}
