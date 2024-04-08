<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockController;

use App\Http\Controllers\TransactionsController;

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
Route::delete('/suppliers/{supplier}', [TransactionsController::class, 'supplierDestroy'])->name('supplier.destroy');
