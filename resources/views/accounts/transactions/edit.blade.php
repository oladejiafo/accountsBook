@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Transaction</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="type">Transaction Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Deposit" {{ $transaction->type == 'Deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="Expense" {{ $transaction->type == 'Expense' ? 'selected' : '' }}>Expense</option>
                        <option value="Withdrawal" {{ $transaction->type == 'Withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="Others" {{ $transaction->type == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="account_id">Account:</label>
                    <select class="form-control" id="account_id" name="account_id" required>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ $transaction->account_id == $account->id ? 'selected' : '' }}>{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $transaction->date }}" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" class="form-control" id="amount" name="amount" value="{{ $transaction->amount }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $transaction->description }}</textarea>
                </div>
                <br>
            
                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Update</button>
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                    </form>
                    
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
