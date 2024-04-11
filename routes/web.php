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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [HomeController::class, 'about'])->name('about');

############ INVENTORY AND STOCKS
Route::get('/inventory', [StockController::class, 'index'])->name('inventory');
Route::get('/stock/new', [StockController::class, 'create'])->name('new-stock');
Route::post('/stock/store', [StockController::class, 'store'])->name('store-stock');
Route::get('/stock/{id}/edit', [StockController::class, 'edit'])->name('edit-stock');
Route::put('/stock/{id}/update', [StockController::class, 'update'])->name('update-stock');
Route::delete('/stock/{id}/delete', [StockController::class, 'destroy'])->name('delete-stock');

########### TRANSACTIONS 
Route::get('sales', [TransactionsController::class, 'salesIndex'])->name('sales.index');
Route::get('/sales/new', [TransactionsController::class, 'salesCreate'])->name('sales.create');
Route::post('sales/store', [TransactionsController::class, 'salesStore'])->name('sales.store');
Route::delete('sales/{sale}', [TransactionsController::class, 'salesDestroy'])->name('sales.destroy');
Route::get('/bill/{id}', [TransactionsController::class, 'salesShow'])->name('sales.show');
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


############# ACCOUNTS 

Route::get('/account/dashboard', [AccountsController::class, 'dashboard'])->name('account.dashboard');

Route::prefix('ledger')->group(function () {
    Route::get('/', [AccountsController::class, 'ledgerIndex'])->name('ledger.index');
    Route::get('/create', [AccountsController::class, 'ledgerCreate'])->name('ledger.create');
    Route::post('/store', [AccountsController::class, 'ledgerStore'])->name('ledger.store');
    Route::get('/edit/{id}', [AccountsController::class, 'ledgerEdit'])->name('ledger.edit');
    Route::put('/update/{id}', [AccountsController::class, 'ledgerUpdate'])->name('ledger.update');
    Route::delete('/delete/{id}', [AccountsController::class, 'ledgerDestroy'])->name('ledger.destroy');
});

Route::prefix('transactions')->group(function () {
    Route::get('/', [AccountsController::class, 'transactionsIndex'])->name('transactions.index');
    Route::get('/create', [AccountsController::class, 'transactionsCreate'])->name('transactions.create');
    Route::post('/store', [AccountsController::class, 'transactionsStore'])->name('transactions.store');
    Route::get('/edit/{id}', [AccountsController::class, 'transactionsEdit'])->name('transactions.edit');
    Route::put('/update/{id}', [AccountsController::class, 'transactionsUpdate'])->name('transactions.update');
    Route::get('/delete/{id}', [AccountsController::class, 'transactionsDestroy'])->name('transactions.destroy');
});

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

Route::prefix('taxes')->group(function () {
    // Define routes for taxes module
});

Route::get('/chart-of-accounts', [AccountsController::class, 'chartOfAccounts'])->name('chartOfAccounts');
Route::post('/chart-of-accounts/upload', [AccountsController::class, 'uploadChartOfAccounts'])->name('chartOfAccounts.upload');

Route::get('/chart-of-accounts/create', [AccountsController::class, 'createChartOfAccount'])->name('chartOfAccounts.create');
Route::post('/chart-of-accounts/store', [AccountsController::class, 'storeChartOfAccount'])->name('chartOfAccounts.store');
Route::get('/chart-of-accounts/edit/{id}', [AccountsController::class, 'editChartOfAccount'])->name('chartOfAccounts.edit');
Route::put('/chart-of-accounts/update/{id}', [AccountsController::class, 'updateChartOfAccount'])->name('chartOfAccounts.update');
Route::get('/chart-of-accounts/delete/{id}', [AccountsController::class, 'deleteChartOfAccount'])->name('chartOfAccounts.destroy');

//bank feeds
Route::group(['middleware' => 'auth'], function () {
    Route::get('/bank-feeds', [AccountsController::class, 'indexBankFeeds'])->name('bank-feeds.index');
    Route::post('/bank-feeds/upload', [AccountsController::class, 'uploadBankFeeds'])->name('bank-feeds.upload');
});

Route::get('/reconciliation', [AccountsController::class, 'Reconsindex'])->name('reconciliation.index');
Route::post('/reconciliation/match', [AccountsController::class, 'matchTransactions'])->name('reconciliation.match');


