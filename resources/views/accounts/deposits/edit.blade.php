@extends('layouts.app')

@section('title', 'Edit Deposit')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Deposit</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('deposits.update', $deposit->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="type">Deposit Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Regular Deposit" {{ old('type') == 'Regular Deposit' ? 'selected' : '' }}>Regular Deposit</option>
                        <option value="Cash Deposit" {{ old('type') == 'Cash Deposit' ? 'selected' : '' }}>Cash Deposit</option>
                        <option value="Check Deposit" {{ old('type') == 'Check Deposit' ? 'selected' : '' }}>Check Deposit</option>
                        <option value="Direct Deposit" {{ old('type') == 'Direct Deposit' ? 'selected' : '' }}>Direct Deposit</option>
                        <option value="Mobile Deposit" {{ old('type') == 'Mobile Deposit' ? 'selected' : '' }}>Mobile Deposit</option>
                        <option value="ATM Deposit" {{ old('type') == 'ATM Deposit' ? 'selected' : '' }}>ATM Deposit</option>
                        <option value="Online Transfer" {{ old('type') == 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                        <option value="Wire Transfer" {{ old('type') == 'Wire Transfer' ? 'selected' : '' }}>Wire Transfer</option>
                        <option value="Automatic Deposit" {{ old('type') == 'Automatic Deposit' ? 'selected' : '' }}>Automatic Deposit</option>
                        <option value="Refund Deposit" {{ old('type') == 'Refund Deposit' ? 'selected' : '' }}>Refund Deposit</option>
                        <option value="Security Deposit" {{ old('type') == 'Security Deposit' ? 'selected' : '' }}>Security Deposit</option>
                    </select>                    
                </div>
                <div class="form-group">
                    <label for="account_id">Account:</label>
                    <select class="form-control" id="account_id" name="account_id" required>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ $deposit->account_id == $account->id ? 'selected' : '' }}>{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $deposit->date }}" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" class="form-control" id="amount" name="amount" value="{{ $deposit->amount }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $deposit->description }}</textarea>
                </div>
                <br>
            
                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('deposits.destroy', $deposit->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
