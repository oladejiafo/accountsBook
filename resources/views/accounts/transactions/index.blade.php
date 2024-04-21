@extends('layouts.app')

@section('title', 'Accounts Transactions')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Transactions</div>
        <div class="col-md-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('transactions.create') }}" class="btn btn-success mb-3">Create Transaction</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('transactions.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for transactions">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink">Search</button>
            </div>
        </div>
    </form>
    
    <br>

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
                    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                    </form>
                </td>
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
