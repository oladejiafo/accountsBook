@extends('layouts.app')

@section('title', 'Accounts Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-12" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">
            @if(request()->has('search_account') && request('search_account') != 'all')
                {{ ucfirst(request('search_account')) }} Ledger
            @else
                General Ledger
            @endif
        </div>
    </div>
    <br>

    <div style="border-bottom: 1px solid rgb(91, 89, 89);">
        <form method="GET" action="{{ route('ledger.index') }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Search... date, keywords etc">
                </div>
                <div class="form-group col-md-3">
                    <select name="search_account" class="form-control">
                        <option value="all">Select Account Type</option>
                        @foreach($accountCategories as $category)
                            <option value="{{ $category->category }}">{{ $category->category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <select name="search_date" class="form-control">
                        <option value="all">Select Duration</option>
                        <option value="today">Today</option>
                        <option value="last_7_days">Last 7 days</option>
                        <option value="last_30_days">Last 30 days</option>
                        <option value="last_60_days">Last 60 days</option>
                        <option value="last_90_days">Last 90 days</option>
                        <option value="current_month">Current Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_3_months">Last 3 Months</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-info">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row mt-2">
        <div class="col-md-6">
                <div class="mb-3 text-center" style="padding-left: 1%; padding-right: 39%">
                    <strong  class="bg-dark text-white d-block mb-1 pb-2 pt-2">Account Name:</strong>
                    <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229);border: 1px solid #ccc;">
                        @if(request()->has('search_account') && request('search_account') != 'all')
                            @if($transactions->isNotEmpty() && $transactions->first()->account)
                                {{ $transactions->first()->account->type }}
                            @else
                                General Ledger
                            @endif
                        @else
                            General Ledger
                        @endif
                    </div>
                </div>
                <div class="mb-3 text-center" style="padding-left: 1%; padding-right: 39%;">
                    <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Account Code:</strong>
                    <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229);border: 1px solid #ccc;">
                        @if(request()->has('search_account') && request('search_account') != 'all')
                            @if($transactions->isNotEmpty() && $transactions->first()->account)
                                {{ $transactions->first()->account->code }} &nbsp;
                            @else
                                ---
                            @endif
                        @else
                         ---
                        @endif
                    </div>
                </div>
        </div>
        <div class="col-md-6 justify-content-end">
            <div class="mb-3 text-center" style="padding-left: 39%; padding-right: 1%">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Starting Balance:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229);border: 1px solid #ccc;">{{ number_format($startingBalance, 2) }}</div>
            </div>
            <div class="mb-3 text-center" style="padding-left: 39%; padding-right: 1%;">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Total Adjusted Balance:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229);border: 1px solid #ccc;">{{ number_format($totalAdjustedBalance, 2) }}</div>
            </div>
        </div>
    </div>
    
    <hr>


    <table class="table table-css table-bordered table-hover">
        <thead class="thead-light align-middle">
            <tr>
                <th colspan="3">&nbsp;</th>
                <th colspan="2">TRANSACTIONS</th>
                <th colspan="2">BALANCES</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Ref #</th>
                <th>Description</th>
                <th>DR</th>
                <th>CR</th>
                <th>Total Debit</th>
                <th>Total Credit</th>
            </tr>
        </thead>
        <tbody  class="align-middle">
            @foreach($transactions as $transaction)
            <tr> 
                <td>{{ $transaction->date }}</td>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->description }}</td>
                <td>
                    @if(strtolower($transaction->type) === 'income' || strtolower(optional($transaction->account)->category) === 'income')
                        {{ number_format($transaction->amount, 2) }} {{-- Display as credit --}}
                    @elseif(strtolower($transaction->type) === 'asset' || strtolower(optional($transaction->account)->category) === 'asset')
                        0.00 {{-- Display as debit --}}
                    @elseif(strtolower($transaction->type) === 'expense' || strtolower(optional($transaction->account)->category) === 'expense')
                        {{ number_format($transaction->amount, 2) }} {{-- Display as debit --}}
                    @else
                        0.00
                    @endif
                </td>
                <td>
                    @if(strtolower($transaction->type) === 'expense' || strtolower(optional($transaction->account)->category) === 'expense')
                        {{ number_format($transaction->amount, 2) }} {{-- Display as debit --}}
                    @elseif(strtolower($transaction->type) === 'liability' || strtolower(optional($transaction->account)->category) === 'liability')
                        {{ number_format($transaction->amount, 2) }} {{-- Display as credit --}}
                    @elseif(strtolower($transaction->type) === 'equity' || strtolower(optional($transaction->account)->category) === 'equity')
                        {{ number_format($transaction->amount, 2) }} {{-- Display as credit --}}
                    @else
                        0.00
                    @endif
                </td>
                
                <td>{{ number_format($totalDebit,2) }}</td>
                <td>{{ number_format($totalCredit,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if ($transactions->isNotEmpty())
        <div class="pagination">
            {{ $transactions->links() }}
        </div>  
    @endif
</div>

@endsection
