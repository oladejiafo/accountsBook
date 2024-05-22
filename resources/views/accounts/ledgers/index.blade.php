@extends('layouts.app')

@section('title', 'Accounts Transactions')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 titles" style="color: #4e4e4e; font-style: bold; ">
            @if(isset($ttype))
                @if($ttype == "Account Receivable")
                    Accounts Receivable Ledger
                @elseif($ttype == "Account Payable")
                    Accounts Payable Ledger
                @else
                    General Ledger
                @endif
            @else
                @if(request()->has('search_account') && request('search_account') != 'all')
                    {{ ucfirst(request('search_account')) }} Ledger
                @else
                    General Ledger
                @endif
            @endif
        </div>
    </div>
    <br>

    <div style="border-bottom: 1px solid rgb(91, 89, 89);">

        @if($ttype == 'General Ledger')
            <form method="GET" action="{{ route('ledger.general_ledger') }}">
        @elseif($ttype == 'Account Payable')
            <form method="GET" action="{{ route('ledger.accounts_payable_ledger') }}">
        @elseif($ttype == 'Account Receivable')
            <form method="GET" action="{{ route('ledger.accounts_receivable_ledger') }}">
        @else
            <form method="GET" action="{{ route('ledger.index') }}">
        @endif        
            <div class="form-row">
                <div class="form-group col-md-4 col-lg-4 col-sm-12">
                    <input type="text" name="keyword" class="form-control" placeholder="Search... date, keywords etc">
                </div>
                @if(!isset($ttype))
                    <div class="form-group  col-md-3 col-lg-3 col-sm-12">
                        <select name="search_account" class="form-control">
                            <option value="all">Select Account Type</option>
                            @foreach($accountCategories as $category)
                                <option value="{{ $category->category }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group  col-md-3 col-lg-3 col-sm-12">
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
                <div class="form-group col-md-2 col-lg-2 col-sm-12 text-right">
                    <button type="submit" class="btn btn-info" style="width: 100%">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row mt-2">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="mb-3 text-center">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Account Name:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229); border: 1px solid #ccc;">
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
            <div class="mb-3 text-center">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Account Code:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229); border: 1px solid #ccc;">
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
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="mb-3 text-center">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Starting Balance:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229); border: 1px solid #ccc;">{{ number_format($startingBalance, 2) }}</div>
            </div>
            <div class="mb-3 text-center">
                <strong class="bg-dark text-white d-block mb-1 pb-2 pt-2">Total Adjusted Balance:</strong>
                <div class="bg-light p-1 rounded shadow-sm" style="background-color: rgb(220, 234, 229); border: 1px solid #ccc;">{{ number_format($totalAdjustedBalance, 2) }}</div>
            </div>
        </div>
    </div>
    
    <hr>

    <div class="table-responsive">
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
                    @if(strtolower($transaction->type) === 'asset' || strtolower($transaction->type) === 'expense')
                        {{ number_format($transaction->amount, 2) }}
                    @elseif(strtolower($transaction->type) === 'income' || strtolower($transaction->type) === 'liability' || strtolower($transaction->type) === 'equity')
                        0.00 
                    @else
                        0.00
                    @endif
                </td>
                <td>
                    @if(strtolower($transaction->type) === 'income' || strtolower($transaction->type) === 'liability' || strtolower($transaction->type) === 'equity')
                        {{ number_format($transaction->amount, 2) }} 
                    @elseif(strtolower($transaction->type) === 'asset' || strtolower($transaction->type) === 'expense')
                        0.00 
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
    </div>

    @if ($transactions->isNotEmpty())
        <div class="pagination">
            {{ $transactions->links() }}
        </div>  
    @endif
</div>

@endsection
