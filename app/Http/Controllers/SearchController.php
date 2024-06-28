<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Models\User;
use App\Models\Stock;
use App\Models\Transaction;
use Spatie\Searchable\Search;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $query = $request->input('query');

        $results = (new Search())
            ->registerModel(User::class, ['name','email'])
            ->registerModel(Stock::class, ['name', 'description']) 
            ->registerModel(Stock::class, ['type', 'description', 'reference_number', 'transaction_name', 'recipient_name'])
            ->search($query);

        return response()->json($results);
    }    

    public function getSearchSuggestions(Request $request)
    {
        $query = $request->input('query');

        $userResults = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();

        $productResults = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        // Example: Format results to a common structure
        $formattedResults = [
            'users' => $userResults->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'type' => 'User',
                    'url' => route('user.show', ['user' => $user->id]),
                ];
            }),
            'products' => $productResults->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type' => 'Product',
                    'url' => route('product.show', ['product' => $product->id]),
                ];
            }),
        ];

        return response()->json($formattedResults);
    }


}
