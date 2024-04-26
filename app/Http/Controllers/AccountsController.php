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

use App\Models\TaxRate;
use App\Models\TaxTransaction;
use App\Models\TaxCode;
use App\Models\TaxPayment;
use App\Models\TaxSetting;
use App\Models\TaxExemption;

use App\Imports\ChartImportClass;
use App\Imports\BankImportClass;

use Maatwebsite\Excel\Facades\Excel;
// use Spatie\SimpleExcel\SimpleExcelReader;
use Carbon\Carbon;


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
    
        // // Pass data to the dashboard view
        // return view('accounts.dashboard', [
        //     'income' => $income,
        //     'expenses' => $expenses,
        //     'cashFlow' => $cashFlow,
        //     'accountBalances' => $accountBalances,
        //     'alerts' => $alerts,
        // ]);

        // Calculate income
        $income = Transaction::where('company_id', $companyId)->where('type', 'income')->sum('amount');
        
        // Calculate expenses
        $expenses = Transaction::where('company_id', $companyId)->where('type', 'expense')->sum('amount');
        
        // Calculate cash flow
        $cashFlow = $income - $expenses;
        
        // Calculate account balances
        $accountBalances = [
            'assets' => Transaction::where('company_id', $companyId)->where('type', 'asset')->sum('amount'),
            'liabilities' => Transaction::where('company_id', $companyId)->where('type', 'liability')->sum('amount'),
            'equity' => Transaction::where('company_id', $companyId)->where('type', 'equity')->sum('amount'),
        ];

        // // Fetch data for each month
        // $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];

        // $incomeData = [];
        // $expenseData = [];

        // foreach ($months as $month) {
        //     $income = Transaction::where('company_id', $companyId)->where('type', 'income')
        //         ->whereMonth('created_at', '=', date('m', strtotime($month)))
        //         ->sum('amount');
            
        //     $expenses = Transaction::where('company_id', $companyId)->where('type', 'expense')
        //         ->whereMonth('created_at', '=', date('m', strtotime($month)))
        //         ->sum('amount');

        //     $incomeData[] = $income;
        //     $expenseData[] = $expenses;
        // }

        // Fetch data for each month
        $months = [];
        $currentMonth = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $months[] = $currentMonth->format('F Y');
            $currentMonth = $currentMonth->subMonth();
        }
        $currentMonth = Carbon::now()->startOfMonth(); 
        // Add line breaks to the labels
        $monthsWithLineBreaks = array_map(function ($month) {
            return str_replace(' ', "\n", $month);
        }, $months);
        // dd($monthsWithLineBreaks);
        $incomeData = [];
        $expenseData = [];

        foreach ($months as $month) {
            $incomez = Transaction::where('company_id', $companyId)
                ->where('type', 'income')
                ->whereYear('created_at', $currentMonth->year)
                ->whereMonth('created_at', $currentMonth->month)
                ->sum('amount');
                
            $expensez = Transaction::where('company_id', $companyId)->where('type', 'expense')
                ->whereYear('created_at', $currentMonth->year)
                ->whereMonth('created_at', $currentMonth->month)
                ->sum('amount');

            $incomeData[] = $incomez;
            $expenseData[] = $expensez;

            $currentMonth = $currentMonth->subMonth();
        }
        // Construct the $incomeExpensesData array
        $incomeExpensesData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];

        return view('home.dashboard', compact('income', 'expenses', 'cashFlow', 'accountBalances','alerts','incomeExpensesData'));
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
        $transactions = $transactionsQuery->paginate(30);

        $totalDebit = 0;
        $totalCredit = 0;

        // Calculate starting balance based on transactions
        $startingBalance = 0;

        foreach ($transactions as $transaction) {
            // Determine transaction type and account category (case-insensitive)
            $transactionType = strtolower($transaction->type);
            $accountCategory = strtolower(optional($transaction->account)->category);

            // Increment total debit and total credit based on transaction type and account category
            if (in_array($transactionType, ['income', 'liability', 'equity']) || in_array($accountCategory, ['income', 'liability', 'equity'])) {
                $totalCredit += abs($transaction->amount);
            } elseif (in_array($transactionType, ['expense', 'asset']) || in_array($accountCategory, ['expense', 'asset'])) {
                $totalDebit += abs($transaction->amount);
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

        $ttype = null;

        // Return the view with the transactions, account categories, and calculated values
        return view('accounts.ledgers.index', compact('transactions', 'accountCategories', 'totalDebit', 'totalCredit', 'totalAdjustedBalance','startingBalance','ttype'));
    }

    public function generalLedger(Request $request)
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

        // if ($searchAccount && $searchAccount != 'all') {
        //     $transactionsQuery->whereHas('account', function ($accountQuery) use ($searchAccount) {
        //         // dd($searchAccount);
        //         $accountQuery->where(function ($query) use ($searchAccount) {
        //             $query->where('type', 'like', '%' . $searchAccount . '%')
        //                   ->orWhere('category', 'like', '%' . $searchAccount . '%');
        //         });
        //     });
        // }
        
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
        $transactions = $transactionsQuery->paginate(30);

        $totalDebit = 0;
        $totalCredit = 0;

        // Calculate starting balance based on transactions
        $startingBalance = 0;

        foreach ($transactions as $transaction) {
            // Determine transaction type and account category (case-insensitive)
            $transactionType = strtolower($transaction->type);
            $accountCategory = strtolower(optional($transaction->account)->category);

            // Increment total debit and total credit based on transaction type and account category
            if (in_array($transactionType, ['income', 'liability', 'equity']) || in_array($accountCategory, ['income', 'liability', 'equity'])) {
                $totalCredit += abs($transaction->amount);
            } elseif (in_array($transactionType, ['expense', 'asset']) || in_array($accountCategory, ['expense', 'asset'])) {
                $totalDebit += abs($transaction->amount);
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

        $ttype = "General Ledger"; 
        // Calculate total adjusted balance
        $totalAdjustedBalance = $startingBalance + $transactions->sum('amount');

        // Return the view with the transactions, account categories, and calculated values
        return view('accounts.ledgers.index', compact('transactions', 'accountCategories', 'totalDebit', 'totalCredit', 'totalAdjustedBalance','startingBalance','ttype'));
    }

    public function accountsReceivable(Request $request)
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
        $searchAccount = "Account Receivable"; //$request->input('search_account');
        $searchDate = $request->input('search_date');
    
        // Start building the query
        $transactionsQuery = Transaction::where('company_id', $companyId)->where('type', 'income');
    
        // Apply filters based on form inputs
        if ($keyword) {
            $transactionsQuery->where(function ($query) use ($keyword) {
                $query->where('description', 'like', '%' . $keyword . '%')
                    ->orWhere('date', 'like', '%' . $keyword . '%');
            });
        }
    
        // if ($searchAccount && $searchAccount != 'all') {
        //     $transactionsQuery->whereHas('account', function ($accountQuery) use ($searchAccount) {
        //         // dd($searchAccount);
        //         $accountQuery->where(function ($query) use ($searchAccount) {
        //             $query->where('type', 'like', '%' . $searchAccount . '%');
        //         });
        //     });
        // }
        
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
        $transactions = $transactionsQuery->paginate(30);
    
        $totalDebit = 0;
        $totalCredit = 0;
    
        // Calculate starting balance based on transactions
        $startingBalance = 0;
    
        foreach ($transactions as $transaction) {
            // Increment total credit based on transaction amount
            $totalCredit += abs($transaction->amount);
    
            // Adjust starting balance based on transaction amount
            $startingBalance += $transaction->amount;
        }
    
        // Calculate total adjusted balance
        $totalAdjustedBalance = $startingBalance;
        $ttype = "Account Receivable";
    
        // Return the view with the transactions, account categories, and calculated values
        return view('accounts.ledgers.index', compact('transactions', 'accountCategories', 'totalDebit', 'totalCredit', 'totalAdjustedBalance','startingBalance','ttype'));
    }
    
    public function accountsPayable(Request $request)
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
        $searchAccount = "Accounts Payable"; //$request->input('search_account');
        $searchDate = $request->input('search_date');
    
        // Start building the query
        $transactionsQuery = Transaction::where('company_id', $companyId)->where('type', 'expense');
    
        // Apply filters based on form inputs
        if ($keyword) {
            $transactionsQuery->where(function ($query) use ($keyword) {
                $query->where('description', 'like', '%' . $keyword . '%')
                    ->orWhere('date', 'like', '%' . $keyword . '%');
            });
        }
    
        // if ($searchAccount && $searchAccount != 'all') {
        //     $transactionsQuery->whereHas('account', function ($accountQuery) use ($searchAccount) {
        //         // dd($searchAccount);
        //         $accountQuery->where(function ($query) use ($searchAccount) {
        //             $query->where('type', 'like', '%' . $searchAccount . '%');
        //         });
        //     });
        // }
        
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
        $transactions = $transactionsQuery->paginate(30);
    
        $totalDebit = 0;
        $totalCredit = 0;
    
        // Calculate starting balance based on transactions
        $startingBalance = 0;
    
        foreach ($transactions as $transaction) {
            // Increment total debit based on transaction amount
            $totalDebit += abs($transaction->amount);
    
            // Adjust starting balance based on transaction amount
            $startingBalance += $transaction->amount;
        }
    
        // Calculate total adjusted balance
        $totalAdjustedBalance = $startingBalance;

        $ttype = "Account Payable";
    
        // Return the view with the transactions, account categories, and calculated values
        return view('accounts.ledgers.index', compact('transactions', 'accountCategories', 'totalDebit', 'totalCredit', 'totalAdjustedBalance','startingBalance','ttype'));
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
        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('code')->get();
        return view('accounts.transactions.create', compact('accounts','transactionTypes'));
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
            'reference_number' => 'nullable|string',
            'to_account_id' =>  'nullable',
            'source' =>  'nullable|string', 
            'status' =>  'nullable|string',
        ]);

        $validatedData['company_id'] = auth()->user()->company_id;

        Transaction::create($validatedData);

        
        // Create transaction entries based on transaction mapping for sales
        $transactionMapping = TransactionAccountMapping::where('transaction_type', $request->transaction_name)
            ->where('company_id', $companyId)
            ->get();

        foreach ($transactionMapping as $mapping) {
            // Retrieve the accounts associated with the mapping
            $debitAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->debit_account_id)
                ->first();

            $creditAccount = ChartOfAccount::where('company_id', $companyId)
                ->where('id', $mapping->credit_account_id)
                ->first();

            if ($debitAccount && $creditAccount) {
                if ($creditAccount->account_id ==  $request->account_id) {
                    // Create debit transaction entry
                    $debitTransaction = new Transaction();
                    $debitTransaction->reference_number = $request->reference_number;
                    $debitTransaction->account_id = $mapping->debit_account_id;
                    $debitTransaction->amount = $request->amount;
                    $debitTransaction->type = $debitAccount->type; 
                    $debitTransaction->date = $request->date;
                    $debitTransaction->company_id = $companyId;
                    $debitTransaction->name = $request->transaction_name;
                    $debitTransaction->description = $request->description;
                    $debitTransaction->save();
                } elseif ($debitAccount->account_id ==  $request->account_id) {
                    // Create credit transaction entry
                    $creditTransaction = new Transaction();
                    $creditTransaction->reference_number = $request->reference_number;
                    $creditTransaction->account_id = $mapping->credit_account_id;
                    $creditTransaction->amount = $request->amount;
                    $creditTransaction->type = $creditAccount->type; 
                    $creditTransaction->date = $request->date;
                    $creditTransaction->company_id = $companyId;
                    $creditTransaction->name = $request->transaction_name;
                    $creditTransaction->description = $request->description;
                    $creditTransaction->save();
                }
            }
        }

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
        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $transaction = Transaction::where('company_id', $companyId)->findOrFail($id);
        $accounts = ChartOfAccount::where('company_id', $companyId)->orderBy('category')->get();
        return view('accounts.transactions.edit', compact('transaction','accounts','transactionTypes'));
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
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
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
        'debit_account_id' => 'required',
        'credit_account_id' => 'required',
        ]);
        $validatedData['company_id'] = auth()->user()->company_id;
        // Find and update the mapping
        $mapping = TransactionAccountMapping::where('company_id', $companyId)->findOrFail($id);
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

    // Tax Rates
    public function taxRatesIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $tab = request()->query('tab');
        $taxRates = TaxRate::where('company_id', $companyId)->get();
        return view('accounts.tax.ratesIndex', compact('taxRates', 'tab'));
    }

    public function taxRatesCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
   
        $tab = request()->query('tab');

        return view('accounts.tax.ratesCreate', compact('tab'));
    }

    public function taxRatesStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
   
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required',
            'rate' => 'required|numeric',
            'position' => 'nullable',
            'min_earnings' => 'nullable|numeric',
            'max_earnings' => 'nullable|numeric',
            // Add more validation rules as needed
        ]);

        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;

        // Create a new tax rate
        TaxRate::create($validatedData);

        // Determine the tab value
        $tab = $request->query('tab');

        // Redirect back to the index page with the appropriate tab selected
        if ($tab && ($tab == 'Employee')) {
            return redirect()->route('tax-rates.index', ['tab' => 'Employee'])->with('success', 'Tax rate created successfully.');
        } else {
            return redirect()->route('tax-rates.index')->with('success', 'Tax rate created successfully.');
        }
            // return redirect()->route('tax-rates.index')->with('success', 'Tax rate created successfully.');
    }

    public function taxRatesEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->
   
        $tab = request()->query('tab');

        $taxRate = TaxRate::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.ratesEdit', compact('taxRate','tab'));
    }

    public function taxRatesUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required',
            'rate' => 'required|numeric',
            'position' => 'nullable',
            'min_earnings' => 'nullable|numeric',
            'max_earnings' => 'nullable|numeric',
        ]);

        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;
        // Find the tax rate
        $taxRate = TaxRate::where('company_id', $companyId)->findOrFail($id);

        // Update the tax rate
        $taxRate->update($validatedData);
        // Determine the tab value
        $tab = $request->query('tab');

        // Redirect back to the index page with the appropriate tab selected
        if ($tab && ($tab == 'Employee')) {
            return redirect()->route('tax-rates.index', compact('tab'))->with('success', 'Tax rate updated successfully.');
        } else {
            return redirect()->route('tax-rates.index')->with('success', 'Tax rate updated successfully.');
        }
    }

    public function taxRatesDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        // Find the tax rate
        $taxRate = TaxRate::where('company_id', $companyId)->findOrFail($id);

        // Delete the tax rate
        $taxRate->delete();

        return redirect()->route('tax-rates.index')->with('success', 'Tax rate deleted successfully.');
    }

    public function taxTransactionsIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        // $tab = request()->query('tab');
        $taxTransactions = TaxTransaction::where('company_id', $companyId)->get();
        return view('accounts.tax.transactionsIndex', compact('taxTransactions'));
    }

    public function taxTransactionsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $transactionTypes = TransactionType::where('company_id', $companyId)->get();
        $taxCodes = TaxCode::where('company_id', $companyId)->get();
        return view('accounts.tax.transactionsCreate', compact('transactionTypes','taxCodes'));
    }

    public function taxTransactionsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'transaction_type' => 'required|string',
            // 'transaction_type' => 'required|exists:transaction_types,id',
            'tax_code_id' => 'nullable',
            // 'tax_code_id' => 'required|exists:tax_codes,id',
            // Add more validation rules as needed
        ]);
    
        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;
    
        // Create a new tax transaction
        TaxTransaction::create($validatedData);
    
        return redirect()->route('tax-transactions.index')->with('success', 'Tax transaction created successfully.');
    }
    

    public function taxTransactionsShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $tab = request()->query('tab');
        $taxTransaction = TaxTransaction::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.transactionsShow', compact('taxTransaction', 'tab'));
    }

    public function taxTransactionsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $transactionTypes = TransactionType::where('company_id', $companyId)->get(); 
        $taxTransaction = TaxTransaction::where('company_id', $companyId)->findOrFail($id);
        $taxCodes = TaxCode::where('company_id', $companyId)->get();
        return view('accounts.tax.transactionsEdit', compact('taxTransaction', 'transactionTypes','taxCodes'));
    }

    public function taxTransactionsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'transaction_type' => 'required',
            'tax_code_id' => 'required|exists:tax_codes,id',
            // Add more validation rules as needed
        ]);

        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;

        // Find the tax transaction
        $taxTransaction = TaxTransaction::where('company_id', $companyId)->findOrFail($id);

        // Update the tax transaction
        $taxTransaction->update($validatedData);

        return redirect()->route('tax-transactions.index')->with('success', 'Tax transaction updated successfully.');
    }

    public function taxTransactionsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxTransaction = TaxTransaction::where('company_id', $companyId)->findOrFail($id);
        $taxTransaction->delete();
        return redirect()->route('tax-transactions.index')->with('success', 'Tax transaction deleted successfully.');
    }

    // Tax Payments Index
    public function taxPaymentsIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxPayments = TaxPayment::where('company_id', $companyId)->get();
        return view('accounts.tax.paymentsIndex', compact('taxPayments'));
    }

    // Tax Payments Create
    public function taxPaymentsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        return view('accounts.tax.paymentsCreate');
    }

    // Tax Payments Store
    public function taxPaymentsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'tax_type' => 'required|string',
            'reference' => 'nullable|string',
            // 'tax_authority_id' => 'required|exists:tax_authorities,id',
        ]);
        // $validatedData['payment_date'] = date('Y-m-d'); 
        $validatedData['company_id'] = auth()->user()->company_id;
        TaxPayment::create($validatedData);

        return redirect()->route('tax-payments.index')->with('success', 'Tax payment created successfully.');
    }

    // Tax Payments Show
    public function taxPaymentsShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxPayment = TaxPayment::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.paymentsShow', compact('taxPayment'));
    }

    // Tax Payments Edit
    public function taxPaymentsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxPayment = TaxPayment::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.paymentsEdit', compact('taxPayment'));
    }

    // Tax Payments Update
    public function taxPaymentsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxPayment = TaxPayment::where('company_id', $companyId)->findOrFail($id);
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'tax_type' => 'required|string',
            'reference' => 'nullable|string',
            // 'tax_authority_id' => 'required|exists:tax_authorities,id',
            // Add more validation rules as needed
        ]);
        $validatedData['company_id'] = auth()->user()->company_id;
        $taxPayment->update($validatedData);

        return redirect()->route('tax-payments.index')->with('success', 'Tax payment updated successfully.');
    }

    // Tax Payments Destroy
    public function taxPaymentsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxPayment = TaxPayment::where('company_id', $companyId)->findOrFail($id);
        $taxPayment->delete();

        return redirect()->route('tax-payments.index')->with('success', 'Tax payment deleted successfully.');
    }


    // Tax Settings Index
    public function taxSettingsIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxSettings = TaxSetting::where('company_id', $companyId)->get();
        return view('accounts.tax.settingsIndex', compact('taxSettings'));
    }

    // Tax Settings Create
    public function taxSettingsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        return view('accounts.tax.settingsCreate');
    }

    // Tax Settings Store
    public function taxSettingsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'name' => 'required|string',
            'value' => 'required|string',
            // Add more validation rules as needed
        ]);

        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;

        // Create a new tax setting
        TaxSetting::create($validatedData);

        return redirect()->route('tax-settings.index')->with('success', 'Tax setting created successfully.');
    }

    // Tax Settings Show
    public function taxSettingsShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxSetting = TaxSetting::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.settingsShow', compact('taxSetting'));
    }

    // Tax Settings Edit
    public function taxSettingsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxSetting = TaxSetting::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.settingsEdit', compact('taxSetting'));
    }

    // Tax Settings Update
    public function taxSettingsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'name' => 'required|string',
            'value' => 'required|string',
            // Add more validation rules as needed
        ]);

        // Find the tax setting
        $taxSetting = TaxSetting::where('company_id', $companyId)->findOrFail($id);

        // Update the tax setting
        $taxSetting->update($validatedData);

        return redirect()->route('tax-settings.index')->with('success', 'Tax setting updated successfully.');
    }

    // Tax Settings Destroy
    public function taxSettingsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        // Find the tax setting
        $taxSetting = TaxSetting::where('company_id', $companyId)->findOrFail($id);

        // Delete the tax setting
        $taxSetting->delete();

        return redirect()->route('tax-settings.index')->with('success', 'Tax setting deleted successfully.');
    }

    public function taxExemptionsIndex()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxExemptions = TaxExemption::where('company_id', $companyId)->get();
        return view('accounts.tax.exemptionsIndex', compact('taxExemptions'));
    }

    public function taxExemptionsCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        return view('accounts.tax.exemptionsCreate');
    }

    public function taxExemptionsStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            // Add more validation rules as needed
        ]);

        // Merge the company_id into the request data
        $validatedData['company_id'] = auth()->user()->company_id;

        // Create a new tax exemption
        TaxExemption::create($validatedData);

        return redirect()->route('tax-exemptions.index')->with('success', 'Tax exemption created successfully.');
    }

    public function taxExemptionsShow($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxExemption = TaxExemption::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.exemptionsShow', compact('taxExemption'));
    }

    public function taxExemptionsEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $taxExemption = TaxExemption::where('company_id', $companyId)->findOrFail($id);
        return view('accounts.tax.exemptionsEdit', compact('taxExemption'));
    }

    public function taxExemptionsUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            // Add more validation rules as needed
        ]);

        // Find the tax exemption
        $taxExemption = TaxExemption::where('company_id', $companyId)->findOrFail($id);

        // Update the tax exemption
        $taxExemption->update($validatedData);

        return redirect()->route('tax-exemptions.index')->with('success', 'Tax exemption updated successfully.');
    }

    public function taxExemptionsDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->

        // Find the tax exemption
        $taxExemption = TaxExemption::where('company_id', $companyId)->findOrFail($id);

        // Delete the tax exemption
        $taxExemption->delete();

        return redirect()->route('tax-exemptions.index')->with('success', 'Tax exemption deleted successfully.');
    }
}
