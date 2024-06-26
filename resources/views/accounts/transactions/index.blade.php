@extends('layouts.app')

@section('title', 'Accounts Transactions')
<style>
    @media (max-width: 576px) {
        .container {
            padding:5px !important;
        }
    }
</style>
@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-8  mb-3 mb-sm-0 titles" style="color: #4e4e4e; font-weight: bold; ">Transactions</div>
        <div class="col-4  mb-3 d-flex justify-content-end">
            @can('create', \App\Models\Transaction::class)
            <a href="{{ route('transactions.create') }}" class="btn btn-success">Create Transaction</a>
            @endcan
        </div>
    </div>     

    <div style="border-bottom: 1px solid white;"></div>
    <div style="width:100%">

    </div>
    <form method="GET" action="{{ route('transactions.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for transactions">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    
    <br>

    <div class="table-responsive">
    <table class="table table-css table-bordered table-hover">
        <thead class="thead-dark align-middle">
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Type</th>
                <th>Account Type</th>
                <th>Details</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody  class="align-middle">
            @foreach($transactions as $transaction)
            <tr> 
                <td>{{ $transaction->date }}</td>
                <td>{{ $transaction->transaction_name }}</td>
                <td>{{ $transaction->type }}</td>
                <td>{{ optional($transaction->account)->category ?? '' }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ number_format($transaction->amount,2) }}</td>
                <td>
                    
                    @can('update',$transaction)
                      <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-info">Edit</a>
                    @endcan
                    @can('delete',$transaction)
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                    </form>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    @if ($transactions->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('transactions.index') }}" class="form-inline"> 
                <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                <select name="per_page" id="per_page" class="form-control" style="width: 65px" onchange="document.getElementById('perPageForm').submit();">
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="75" {{ request('per_page') == 75 ? 'selected' : '' }}>75</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <div class="pagination">
                {{ $transactions->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
