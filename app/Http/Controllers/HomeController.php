<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Company;
use App\Models\SaleBill;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = null;
        $companyName = null;

        if (auth()->user()) {
            $companyId = auth()->user()->company_id;
            $company = Company::find($companyId);

            if ($company) {
                $companyName = $company->name;
            } else {
                $companyId =1;
                $companyName = "Demo";
            }
        } else {
            $companyId =1;
            $companyName = "Demo";
        }
        // dd($companyName);
        $labels = [];
        $data = [];
        $catt = [];
        // $datas = Stock::pluck('quantity', 'name')->toArray();
        $stockQuery = Stock::where('company_id', $companyId)
            ->where('is_deleted', false)
            ->orderByDesc('quantity')
            ->take(10)
            ->get();

        foreach ($stockQuery as $item) {
            $labels[] = $item->name;
            $data[] = $item->quantity;
            $catt[] = $item->category->name;
        }
        
        $sales = SaleBill::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $purchases = Purchase::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        
        return view('home.home', compact('labels', 'data','catt', 'sales', 'purchases', 'companyName'));
    }

    public function about()
    {
        return view('home.about');
    }
}
