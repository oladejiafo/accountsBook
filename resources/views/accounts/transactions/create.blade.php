@extends('layouts.app')

@section('title', 'Create Transaction')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create Transaction</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="type">Transaction Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" selected disabled hidden>Select Type</option>
                        <option value="Deposit">Deposit</option>
                        <option value="Expense">Expense</option>
                        <option value="Withdrawal">Withdrawal</option>
                        <option value="Others">Others</option>
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
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
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
