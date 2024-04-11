@extends('layouts.app')

@section('title', 'Create Transfer')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create Transfer</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('transfers.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="type">Select Transfer Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" disabled selected>Select transfer Type</option>
                        <option value="Regular transfer">Regular transfer</option>
                        <option value="Cash transfer">Cash transfer</option>
                        <option value="Check transfer">Check transfer</option>
                        <option value="Direct transfer">Direct transfer</option>
                        <option value="Mobile transfer">Mobile transfer</option>
                        <option value="ATM transfer">ATM transfer</option>
                        <option value="Online Transfer">Online Transfer</option>
                        <option value="Wire Transfer">Wire Transfer</option>
                        <option value="Automatic transfer">Automatic transfer</option>
                        <option value="Refund transfer">Refund transfer</option>
                        <option value="Security transfer">Security transfer</option>
                    </select>                                       
                </div>
                <div class="form-group">
                    <label for="to_account_id">Account Transfered To:</label>
                    <select class="form-control" id="to_account_id" name="to_account_id" required>
                        <option value="" disabled selected>Select Account Transfered To</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="form-group">
                    <label for="from_account_id">Account Transfered From:</label>
                    <select class="form-control" id="from_account_id" name="from_account_id" required>
                        <option value="" disabled selected>Select Account Transfered From</option>
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
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById("to_account_id").value = "";
        document.getElementById("from_account_id").value = "";
        document.getElementById("date").value = "";
        document.getElementById("type").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("description").value = "";
    }
</script>
@endsection
