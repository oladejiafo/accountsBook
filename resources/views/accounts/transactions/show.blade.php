@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold;">Transaction Details</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transaction_name">Transaction Name:</label>
                        <p>{{ $transaction->transaction_name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="type">Transaction Type:</label>
                        <p>{{ $transaction->type }}</p>
                    </div>
                    <div class="form-group">
                        <label for="account_id">Account Classification:</label>
                        <p>{{ $transaction->account->code }} - {{ $transaction->account->description }}</p>
                    </div>
                    @if ($transaction->type == 'Transfer')
                    <div class="form-group">
                        <label for="to_account_id">Account Transferred To:</label>
                        <p>{{ $transaction->toAccount->code }} - {{ $transaction->toAccount->description }}</p>
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Transaction Date:</label>
                        <p>{{ $transaction->date->toDateString() }}</p>
                    </div>
                    <div class="form-group">
                        <label for="amount">Transaction Amount:</label>
                        <p>${{ $transaction->amount }}</p>
                    </div>
                    @if (in_array($transaction->transaction_name, ['Expenditure', 'Cash Payment', 'Others']))
                    <div class="form-group">
                        <label for="source">Source of Fund:</label>
                        <p>{{ $transaction->source }}</p>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <p>{{ $transaction->status }}</p>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="description">Details:</label>
                        <p>{{ $transaction->description }}</p>
                    </div>
                </div>
            </div>

            <br>

            <div class="align-middle">
                <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-info">Edit Transaction</a>
                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Transaction</button>
                </form>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back to Transactions</a>
            </div>
        </div>
    </div>
</div>
@endsection
