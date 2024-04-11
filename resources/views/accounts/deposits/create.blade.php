@extends('layouts.app')

@section('title', 'Create Deposit')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create Deposit</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('deposits.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="type">Select Deposit Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" disabled selected>Select Deposit Type</option>
                        <option value="Regular Deposit">Regular Deposit</option>
                        <option value="Cash Deposit">Cash Deposit</option>
                        <option value="Check Deposit">Check Deposit</option>
                        <option value="Direct Deposit">Direct Deposit</option>
                        <option value="Mobile Deposit">Mobile Deposit</option>
                        <option value="ATM Deposit">ATM Deposit</option>
                        <option value="Online Transfer">Online Transfer</option>
                        <option value="Wire Transfer">Wire Transfer</option>
                        <option value="Automatic Deposit">Automatic Deposit</option>
                        <option value="Refund Deposit">Refund Deposit</option>
                        <option value="Security Deposit">Security Deposit</option>
                    </select>                    
                </div>
                <div class="form-group">
                    <label for="account">Account Type:</label>
                    <select class="form-control" id="account" name="account_id" required>
                        <option value="" disabled selected>Select Account Type</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <br>

                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                    <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById("account").value = "";
        document.getElementById("date").value = "";
        document.getElementById("type").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("description").value = "";
    }
</script>
@endsection
