<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockController;

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

Route::get('/inventory', [StockController::class, 'index'])->name('inventory');
Route::get('/stock/new', [StockController::class, 'create'])->name('new-stock');
Route::post('/stock/store', [StockController::class, 'store'])->name('store-stock');
Route::get('/stock/{id}/edit', [StockController::class, 'edit'])->name('edit-stock');
Route::put('/stock/{id}/update', [StockController::class, 'update'])->name('update-stock');
Route::delete('/stock/{id}/delete', [StockController::class, 'destroy'])->name('delete-stock');