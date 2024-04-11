@extends('layouts.app')

@section('title', 'Edit Transfer')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Transfer</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('transfers.update', $transfer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="type">Transfer Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Regular transfer" {{ old('type') == 'Regular transfer' ? 'selected' : '' }}>Regular transfer</option>
                        <option value="Cash transfer" {{ old('type') == 'Cash transfer' ? 'selected' : '' }}>Cash transfer</option>
                        <option value="Check transfer" {{ old('type') == 'Check transfer' ? 'selected' : '' }}>Check transfer</option>
                        <option value="Direct transfer" {{ old('type') == 'Direct transfer' ? 'selected' : '' }}>Direct transfer</option>
                        <option value="Mobile transfer" {{ old('type') == 'Mobile transfer' ? 'selected' : '' }}>Mobile transfer</option>
                        <option value="ATM transfer" {{ old('type') == 'ATM transfer' ? 'selected' : '' }}>ATM transfer</option>
                        <option value="Online Transfer" {{ old('type') == 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                        <option value="Wire Transfer" {{ old('type') == 'Wire Transfer' ? 'selected' : '' }}>Wire Transfer</option>
                        <option value="Automatic transfer" {{ old('type') == 'Automatic transfer' ? 'selected' : '' }}>Automatic transfer</option>
                        <option value="Refund transfer" {{ old('type') == 'Refund transfer' ? 'selected' : '' }}>Refund transfer</option>
                        <option value="Security transfer" {{ old('type') == 'Security transfer' ? 'selected' : '' }}>Security transfer</option>
                    </select>                                
                </div>
                <div class="form-group">
                    <label for="to_account_id">Account Transfered To:</label>
                    <select class="form-control" id="to_account_id" name="to_account_id" required>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ $transfer->to_account_id == $account->id ? 'selected' : '' }}>{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="from_account_id">Account Transfered From:</label>
                    <select class="form-control" id="from_account_id" name="from_account_id" required>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ $transfer->from_account_id == $account->id ? 'selected' : '' }}>{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $transfer->date }}" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" class="form-control" id="amount" name="amount" value="{{ $transfer->amount }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $transfer->description }}</textarea>
                </div>
                <br>
            
                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('transfers.destroy', $transfer->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
