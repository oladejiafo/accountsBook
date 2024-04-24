<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockController;

use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\AccountsController;

use App\Http\Controllers\Auth\RegisteredUserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/register/company', [RegisteredUserController::class, 'createCompany'])->name('register.company');
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

######### DASHBOARDS
Route::get('/inventoryInsights', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [AccountsController::class, 'dashboard'])->name('account.dashboard');


Route::get('/about', [HomeController::class, 'about'])->name('about');


############ INVENTORY AND STOCKS
Route::get('/inventory', [StockController::class, 'index'])->name('inventory');
Route::get('/stock/new', [StockController::class, 'create'])->name('new-stock');
Route::post('/stock/store', [StockController::class, 'store'])->name('store-stock');
Route::get('/stock/{id}/edit', [StockController::class, 'edit'])->name('edit-stock');
Route::put('/stock/{id}/update', [StockController::class, 'update'])->name('update-stock');
Route::delete('/stock/{id}/delete', [StockController::class, 'destroy'])->name('delete-stock');

########### Sales/Purchases TRANSACTIONS 
Route::get('sales', [TransactionsController::class, 'salesIndex'])->name('sales.index');
Route::get('/sales/new', [TransactionsController::class, 'salesCreate'])->name('sales.create');
Route::post('sales/store', [TransactionsController::class, 'salesStore'])->name('sales.store');
Route::delete('sales/{sale}', [TransactionsController::class, 'salesDestroy'])->name('sales.destroy');
Route::get('/bill/{id}', [TransactionsController::class, 'salesShow'])->name('sales.show');

Route::get('/sales/{id}/edit', [TransactionsController::class, 'salesEdit'])->name('sales.edit');
Route::put('/sales/{id}', [TransactionsController::class, 'salesUpdate'])->name('sales.update');

//Returns
Route::get('/returns', [TransactionsController::class, 'showReturnsForm'])->name('returns.show');
Route::post('/returns', [TransactionsController::class, 'processReturn'])->name('returns.process');
Route::get('/autocomplete/customers', [TransactionsController::class, 'returnCustomers'])->name('autocomplete.customers');
// Route::get('/fetchCustomerTransactions', [TransactionsController::class, 'fetchCustomerTransactions'])->name('fetchCustomerTransactions');
Route::get('/fetch-customer-transactions', [TransactionsController::class, 'fetchCustomerTransactions'])->name('fetchCustomerTransactions');


// Define similar routes for other actions

Route::get('/purchases', [TransactionsController::class, 'purchasesIndex'])->name('purchase.index');
Route::post('/purchases/new', [TransactionsController::class, 'purchasesCreate'])->name('purchase.create');
Route::post('/purchases/store', [TransactionsController::class, 'purchasesStore'])->name('purchase.store');
Route::get('/select-supplier', [TransactionsController::class, 'selectSupplier'])->name('select-supplier');
Route::get('/invoice/{id}', [TransactionsController::class, 'purchasesShow'])->name('purchase.show');
Route::delete('purchases/{sale}', [TransactionsController::class, 'purchasesDestroy'])->name('purchase.destroy');

Route::get('/supplier/{supplier}', [TransactionsController::class, 'supplier'])->name('supplier');
Route::get('/suppliers', [TransactionsController::class, 'supplierIndex'])->name('supplier.index');

Route::get('/suppliers/new', [TransactionsController::class, 'supplierCreate'])->name('supplier.create');
Route::post('/suppliers/store', [TransactionsController::class, 'supplierStore'])->name('supplier.store');
Route::get('/suppliers/{supplier}/edit', [TransactionsController::class, 'supplierEdit'])->name('supplier.edit');
Route::put('/suppliers/{supplier}', [TransactionsController::class, 'supplierUpdate'])->name('supplier.update');
Route::get('/suppliers/{supplier}', [TransactionsController::class, 'supplierDestroy'])->name('supplier.destroy');

//Customers
Route::prefix('customers')->group(function () {
    Route::get('/', [TransactionsController::class, 'customersIndex'])->name('customers.index');
    Route::get('/view/{customer}', [TransactionsController::class, 'customersShow'])->name('customers.show');
    Route::get('/create', [TransactionsController::class, 'customersCreate'])->name('customers.create');
    Route::post('/store', [TransactionsController::class, 'customersStore'])->name('customers.store');
    Route::get('/{customer}/edit', [TransactionsController::class, 'customersEdit'])->name('customers.edit');
    Route::put('/{customer}/update', [TransactionsController::class, 'customersUpdate'])->name('customers.update');
    Route::delete('/{customer}', [TransactionsController::class, 'customersDestroy'])->name('customers.destroy');
});
Route::get('/fetch-customer-details',[TransactionsController::class, 'fetchCustomerDetails'])->name('fetchCustomerDetails');


//Payments
Route::get('/payments', [TransactionsController::class, 'paymentsIndex'])->name('payments.index');
Route::get('/payments/create/{saleId?}', [TransactionsController::class, 'paymentsCreate'])->name('payments.create');
Route::post('/payments', [TransactionsController::class, 'paymentsStore'])->name('payments.store');
Route::get('/payments/{payment}/edit', [TransactionsController::class, 'paymentsEdit'])->name('payments.edit');
Route::put('/payments/{payment}', [TransactionsController::class, 'paymentsUpdate'])->name('payments.update');
Route::delete('/payments/{payment}', [TransactionsController::class, 'paymentsDestroy'])->name('payments.destroy');

Route::get('/get-stock-details/{stock}', [TransactionsController::class, 'getStockDetails'])->name('stocks.details');

############# ACCOUNTS 
Route::prefix('ledger')->group(function () {
    Route::get('/', [AccountsController::class, 'ledgerIndex'])->name('ledger.index');
    Route::get('/general', [AccountsController::class, 'generalLedger'])->name('ledger.general_ledger');
    Route::get('/accounts-receivable', [AccountsController::class, 'accountsReceivable'])->name('ledger.accounts_receivable_ledger');
    Route::get('/accounts-payable', [AccountsController::class, 'accountsPayable'])->name('ledger.accounts_payable_ledger');
    
});

Route::prefix('transactions')->group(function () {
    Route::get('/', [AccountsController::class, 'transactionsIndex'])->name('transactions.index');
    Route::get('/create', [AccountsController::class, 'transactionsCreate'])->name('transactions.create');
    Route::post('/store', [AccountsController::class, 'transactionsStore'])->name('transactions.store');
    Route::get('/edit/{id}', [AccountsController::class, 'transactionsEdit'])->name('transactions.edit');
    Route::put('/update/{id}', [AccountsController::class, 'transactionsUpdate'])->name('transactions.update');
    Route::get('/delete/{id}', [AccountsController::class, 'transactionsDestroy'])->name('transactions.destroy');
});
Route::get('/get-account-classifications', [AccountsController::class, 'getAccountClassifications'])->name('getAccountClassifications');

Route::prefix('deposits')->group(function () {
    Route::get('/', [AccountsController::class, 'depositsIndex'])->name('deposits.index');
    Route::get('/create', [AccountsController::class, 'depositsCreate'])->name('deposits.create');
    Route::post('/store', [AccountsController::class, 'depositsStore'])->name('deposits.store');
    Route::get('/edit/{id}', [AccountsController::class, 'depositsEdit'])->name('deposits.edit');
    Route::put('/update/{id}', [AccountsController::class, 'depositsUpdate'])->name('deposits.update');
    Route::get('/delete/{id}', [AccountsController::class, 'depositsDestroy'])->name('deposits.destroy');
});

Route::prefix('withdrawals')->group(function () {
    Route::get('/', [AccountsController::class, 'withdrawalsIndex'])->name('withdrawals.index');
    Route::get('/create', [AccountsController::class, 'withdrawalsCreate'])->name('withdrawals.create');
    Route::post('/store', [AccountsController::class, 'withdrawalsStore'])->name('withdrawals.store');
    Route::get('/edit/{id}', [AccountsController::class, 'withdrawalsEdit'])->name('withdrawals.edit');
    Route::put('/update/{id}', [AccountsController::class, 'withdrawalsUpdate'])->name('withdrawals.update');
    Route::get('/delete/{id}', [AccountsController::class, 'withdrawalsDestroy'])->name('withdrawals.destroy');
});

Route::prefix('transfers')->group(function () {
    Route::get('/', [AccountsController::class, 'transfersIndex'])->name('transfers.index');
    Route::get('/create', [AccountsController::class, 'transfersCreate'])->name('transfers.create');
    Route::post('/store', [AccountsController::class, 'transfersStore'])->name('transfers.store');
    Route::get('/edit/{id}', [AccountsController::class, 'transfersEdit'])->name('transfers.edit');
    Route::put('/update/{id}', [AccountsController::class, 'transfersUpdate'])->name('transfers.update');
    Route::get('/delete/{id}', [AccountsController::class, 'transfersDestroy'])->name('transfers.destroy');
});


Route::get('/chart-of-accounts', [AccountsController::class, 'chartOfAccounts'])->name('chartOfAccounts');
Route::post('/chart-of-accounts/upload', [AccountsController::class, 'uploadChartOfAccounts'])->name('chartOfAccounts.upload');

Route::get('/chart-of-accounts/create', [AccountsController::class, 'createChartOfAccount'])->name('chartOfAccounts.create');
Route::post('/chart-of-accounts/store', [AccountsController::class, 'storeChartOfAccount'])->name('chartOfAccounts.store');
Route::get('/chart-of-accounts/edit/{id}', [AccountsController::class, 'editChartOfAccount'])->name('chartOfAccounts.edit');
Route::put('/chart-of-accounts/update/{id}', [AccountsController::class, 'updateChartOfAccount'])->name('chartOfAccounts.update');
Route::get('/chart-of-accounts/delete/{id}', [AccountsController::class, 'deleteChartOfAccount'])->name('chartOfAccounts.destroy');

//transaction-account-mapping
Route::prefix('transaction-account-mapping')->group(function () {
    Route::get('/', [AccountsController::class, 'transactionAccountMappingIndex'])->name('transaction-account-mapping.index');
    Route::get('/create', [AccountsController::class, 'transactionAccountMappingCreate'])->name('transaction-account-mapping.create');
    Route::post('/', [AccountsController::class, 'transactionAccountMappingStore'])->name('transaction-account-mapping.store');
    Route::get('/{id}/edit', [AccountsController::class, 'transactionAccountMappingEdit'])->name('transaction-account-mapping.edit');
    Route::put('/{id}', [AccountsController::class, 'transactionAccountMappingUpdate'])->name('transaction-account-mapping.update');
    Route::delete('/{id}', [AccountsController::class, 'transactionAccountMappingDestroy'])->name('transaction-account-mapping.destroy');
});

//bank feeds
Route::group(['middleware' => 'auth'], function () {
    Route::get('/bank-feeds', [AccountsController::class, 'indexBankFeeds'])->name('bank-feeds.index');
    Route::post('/bank-feeds/upload', [AccountsController::class, 'uploadBankFeeds'])->name('bank-feeds.upload');
});

Route::get('/reconciliation', [AccountsController::class, 'Reconsindex'])->name('reconciliation.index');
Route::post('/reconciliation/match', [AccountsController::class, 'matchTransactions'])->name('reconciliation.match');

// Routes for Tax Module
Route::prefix('tax')->group(function () {
    // Tax Rates Routes
    Route::get('/rates', [AccountsController::class, 'taxRatesIndex'])->name('tax-rates.index');
    Route::get('/rates/create', [AccountsController::class, 'taxRatesCreate'])->name('tax-rates.create');
    Route::post('/rates', [AccountsController::class, 'taxRatesStore'])->name('tax-rates.store');
    Route::get('/rates/{id}', [AccountsController::class, 'taxRatesShow'])->name('tax-rates.show');
    Route::get('/rates/{id}/edit', [AccountsController::class, 'taxRatesEdit'])->name('tax-rates.edit');
    Route::put('/rates/{id}', [AccountsController::class, 'taxRatesUpdate'])->name('tax-rates.update');
    Route::delete('/rates/{id}', [AccountsController::class, 'taxRatesDestroy'])->name('tax-rates.destroy');

    // Tax Transactions Routes
    Route::get('/transactions', [AccountsController::class, 'taxTransactionsIndex'])->name('tax-transactions.index');
    Route::get('/transactions/create', [AccountsController::class, 'taxTransactionsCreate'])->name('tax-transactions.create');
    Route::post('/transactions', [AccountsController::class, 'taxTransactionsStore'])->name('tax-transactions.store');
    Route::get('/transactions/{id}', [AccountsController::class, 'taxTransactionsShow'])->name('tax-transactions.show');
    Route::get('/transactions/{id}/edit', [AccountsController::class, 'taxTransactionsEdit'])->name('tax-transactions.edit');
    Route::put('/transactions/{id}', [AccountsController::class, 'taxTransactionsUpdate'])->name('tax-transactions.update');
    Route::delete('/transactions/{id}', [AccountsController::class, 'taxTransactionsDestroy'])->name('tax-transactions.destroy');

    // Tax Payments Routes
    Route::get('/payments', [AccountsController::class, 'taxPaymentsIndex'])->name('tax-payments.index');
    Route::get('/payments/create', [AccountsController::class, 'taxPaymentsCreate'])->name('tax-payments.create');
    Route::post('/payments', [AccountsController::class, 'taxPaymentsStore'])->name('tax-payments.store');
    Route::get('/payments/{id}', [AccountsController::class, 'taxPaymentsShow'])->name('tax-payments.show');
    Route::get('/payments/{id}/edit', [AccountsController::class, 'taxPaymentsEdit'])->name('tax-payments.edit');
    Route::put('/payments/{id}', [AccountsController::class, 'taxPaymentsUpdate'])->name('tax-payments.update');
    Route::delete('/payments/{id}', [AccountsController::class, 'taxPaymentsDestroy'])->name('tax-payments.destroy');

    // Tax Settings Routes
    Route::get('/settings', [AccountsController::class, 'taxSettingsIndex'])->name('tax-settings.index');
    Route::get('/settings/create', [AccountsController::class, 'taxSettingsCreate'])->name('tax-settings.create');
    Route::post('/settings', [AccountsController::class, 'taxSettingsStore'])->name('tax-settings.store');
    Route::get('/settings/{id}', [AccountsController::class, 'taxSettingsShow'])->name('tax-settings.show');
    Route::get('/settings/{id}/edit', [AccountsController::class, 'taxSettingsEdit'])->name('tax-settings.edit');
    Route::put('/settings/{id}', [AccountsController::class, 'taxSettingsUpdate'])->name('tax-settings.update');
    Route::delete('/settings/{id}', [AccountsController::class, 'taxSettingsDestroy'])->name('tax-settings.destroy');
    
    // Tax Exemptions Routes
    Route::get('/exemptions', [AccountsController::class, 'taxExemptionsIndex'])->name('tax-exemptions.index');
    Route::get('/exemptions/create', [AccountsController::class, 'taxExemptionsCreate'])->name('tax-exemptions.create');
    Route::post('/exemptions', [AccountsController::class, 'taxExemptionsStore'])->name('tax-exemptions.store');
    Route::get('/exemptions/{id}', [AccountsController::class, 'taxExemptionsShow'])->name('tax-exemptions.show');
    Route::get('/exemptions/{id}/edit', [AccountsController::class, 'taxExemptionsEdit'])->name('tax-exemptions.edit');
    Route::put('/exemptions/{id}', [AccountsController::class, 'taxExemptionsUpdate'])->name('tax-exemptions.update');
    Route::delete('/exemptions/{id}', [AccountsController::class, 'taxExemptionsDestroy'])->name('tax-exemptions.destroy');

    // Tax Authorities Routes
    Route::resource('authorities', 'AccountsController')->except(['show'])->names([
        'index' => 'tax-authorities.index',
        'create' => 'tax-authorities.create',
        'store' => 'tax-authorities.store',
        'edit' => 'tax-authorities.edit',
        'update' => 'tax-authorities.update',
        'destroy' => 'tax-authorities.destroy',
    ]);

    // Tax Codes Routes
    Route::resource('codes', 'AccountsController')->except(['show'])->names([
        'index' => 'tax-codes.index',
        'create' => 'tax-codes.create',
        'store' => 'tax-codes.store',
        'edit' => 'tax-codes.edit',
        'update' => 'tax-codes.update',
        'destroy' => 'tax-codes.destroy',
    ]);
});

