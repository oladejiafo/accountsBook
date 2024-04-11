@extends('layouts.app')

@section('title', 'Edit Withdrawal')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Withdrawal</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('withdrawals.update', $withdrawal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="type">Withdrawal Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Regular Withdrawal" {{ old('type') == 'Regular Withdrawal' ? 'selected' : '' }}>Regular Withdrawal</option>
                        <option value="Cash Withdrawal" {{ old('type') == 'Cash Withdrawal' ? 'selected' : '' }}>Cash Withdrawal</option>
                        <option value="Check Withdrawal" {{ old('type') == 'Check Withdrawal' ? 'selected' : '' }}>Check Withdrawal</option>
                        <option value="Direct Withdrawal" {{ old('type') == 'Direct Withdrawal' ? 'selected' : '' }}>Direct Withdrawal</option>
                        <option value="Mobile Withdrawal" {{ old('type') == 'Mobile Withdrawal' ? 'selected' : '' }}>Mobile Withdrawal</option>
                        <option value="ATM Withdrawal" {{ old('type') == 'ATM Withdrawal' ? 'selected' : '' }}>ATM Withdrawal</option>
                        <option value="Online Transfer" {{ old('type') == 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                        <option value="Wire Transfer" {{ old('type') == 'Wire Transfer' ? 'selected' : '' }}>Wire Transfer</option>
                        <option value="Automatic Withdrawal" {{ old('type') == 'Automatic Withdrawal' ? 'selected' : '' }}>Automatic Withdrawal</option>
                        <option value="Refund Withdrawal" {{ old('type') == 'Refund Withdrawal' ? 'selected' : '' }}>Refund Withdrawal</option>
                        <option value="Security Withdrawal" {{ old('type') == 'Security Withdrawal' ? 'selected' : '' }}>Security Withdrawal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="account_id">Account:</label>
                    <select class="form-control" id="account_id" name="account_id" required>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ $withdrawal->account_id == $account->id ? 'selected' : '' }}>{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $withdrawal->date }}" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" class="form-control" id="amount" name="amount" value="{{ $withdrawal->amount }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $withdrawal->description }}</textarea>
                </div>
                <br>
            
                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('withdrawals.destroy', $withdrawal->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
