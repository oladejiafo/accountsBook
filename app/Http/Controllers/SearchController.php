<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Services\SearchService;
use App\Models\User;
use App\Models\Stock;
use App\Models\Transaction;

// use App\Models\GeneralLedger;
use App\Models\TaxRate;
use App\Models\TaxTransaction;
use App\Models\TaxPayment;
use App\Models\TaxSetting;
use App\Models\TaxExemption;
use App\Models\ChartOfAccount;
use App\Models\Transfer;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\Customer;

use App\Models\SaleBill;
use App\Models\FixedAsset;
use App\Models\Employee;

use Spatie\Searchable\Search;
use Spatie\Searchable\SearchResult;

class SearchController extends Controller
{
    // protected $searchService;

    // public function __construct(SearchService $searchService)
    // {
    //     $this->searchService = $searchService;
    // }

    public function index(Request $request)
    {
        $query = $request->input('query');

        $results = (new Search())
        ->registerModel(Stock::class, ['name', 'description']) 
        ->registerModel(Transaction::class, ['type', 'description', 'reference_number', 'transaction_name', 'recipient_name'])
        // ->registerModel(GeneralLedger::class, ['type', 'description'])
        ->registerModel(ChartOfAccount::class, ['type', 'code', 'description', 'name'])
        ->registerModel(Transfer::class, ['description'])
        ->registerModel(Deposit::class, ['type', 'description'])
        ->registerModel(Withdrawal::class, ['type', 'description'])
        ->registerModel(PurchaseItem::class, ['billno','stock_id'])
        ->registerModel(Supplier::class, ['name', 'email', 'phone'])
        ->registerModel(Payment::class, ['customer_id', 'sales_id', 'stock_id', 'invoice_id', 'invoice_number', 'bank_reference_number'])
        ->registerModel(Customer::class, ['name', 'email', 'phone'])
        ->registerModel(SaleBill::class, ['name', 'phone'])
        ->registerModel(FixedAsset::class, ['description', 'serial_number', 'asset_code'])
        ->registerModel(Employee::class, ['staff_number', 'sur_name', 'first_name', 'middle_name', 'email', 'phone', 'personal_phone', 'personal_email'])
        ->search($query);


        $formattedResults = [];
        foreach ($results as $result) {
            if ($result->searchable instanceof Stock) {
                $formattedResults[] = [
                    'title' => $result->searchable->name,
                    'type' => 'Stock',
                    'url' => route('edit-stock', $result->searchable->id),
                ];
            } elseif ($result->searchable instanceof Transaction) {
                $formattedResults[] = [
                    'title' => $result->searchable->transaction_name,
                    'type' => 'Transaction',
                    'url' => route('transactions.edit', $result->searchable->id),
                ];
            // } elseif ($result->searchable instanceof Ledger) {
            //     $formattedResults[] = [
            //         'title' => $result->searchable->type,
            //         'type' => 'Ledger',
            //         'url' => route('ledger.index'), // Adjust as per your routes
            //     ];
            } elseif ($result->searchable instanceof ChartOfAccount) {
                $formattedResults[] = [
                    'title' => $result->searchable->name,
                    'type' => 'Chart of Account',
                    'url' => route('chartOfAccounts.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Transfer) {
                $formattedResults[] = [
                    'title' => 'Transfer ' . $result->searchable->description, 
                    'type' => 'Transfer',
                    'url' => route('transfers.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Deposit) {
                $formattedResults[] = [
                    'title' => 'Deposit ' . $result->searchable->type,
                    'type' => 'Deposit',
                    'url' => route('deposits.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Withdrawal) {
                $formattedResults[] = [
                    'title' => 'Withdrawal ' . $result->searchable->type,
                    'type' => 'Withdrawal',
                    'url' => route('withdrawals.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof PurchaseItem) {
                $formattedResults[] = [
                    'title' => 'Purchase ' . $result->searchable->billno,
                    'type' => 'Purchase',
                    'url' => route('purchase.show', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Supplier) {
                $formattedResults[] = [
                    'title' => $result->searchable->name,
                    'type' => 'Supplier',
                    'url' => route('supplier', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Payment) {
                $formattedResults[] = [
                    'title' => 'Payment ' . $result->searchable->invoice_number,
                    'type' => 'Payment',
                    'url' => route('payments.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Customer) {
                $formattedResults[] = [
                    'title' => $result->searchable->name,
                    'type' => 'Customer',
                    'url' => route('customers.show', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof SaleBill) {
                $formattedResults[] = [
                    'title' => $result->searchable->name,
                    'type' => 'Sale',
                    'url' => route('sales.show', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof FixedAsset) {
                $formattedResults[] = [
                    'title' => $result->searchable->asset_code,
                    'type' => 'Fixed Asset',
                    'url' => route('fixed_assets.edit', $result->searchable->id), // Adjust as per your routes
                ];
            } elseif ($result->searchable instanceof Employee) {
                $formattedResults[] = [
                    'title' => $result->searchable->first_name . ' ' . $result->searchable->sur_name,
                    'type' => 'Employee',
                    'url' => route('employees.show', $result->searchable->id), // Adjust as per your routes
                ];
            }

        }

        return response()->json($formattedResults);
    }    

    // public function getSearchSuggestions(Request $request)
    // {
    //     $query = $request->input('query');

    //     $userResults = User::where('name', 'like', "%{$query}%")
    //         ->orWhere('email', 'like', "%{$query}%")
    //         ->get();

    //     $productResults = Product::where('name', 'like', "%{$query}%")
    //         ->orWhere('description', 'like', "%{$query}%")
    //         ->get();

    //     // Example: Format results to a common structure
    //     $formattedResults = [
    //         'users' => $userResults->map(function ($user) {
    //             return [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'type' => 'User',
    //                 'url' => route('user.show', ['user' => $user->id]),
    //             ];
    //         }),
    //         'products' => $productResults->map(function ($product) {
    //             return [
    //                 'id' => $product->id,
    //                 'name' => $product->name,
    //                 'type' => 'Product',
    //                 'url' => route('product.show', ['product' => $product->id]),
    //             ];
    //         }),
    //     ];

    //     return response()->json($formattedResults);
    // }


}
