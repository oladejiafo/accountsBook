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
                    <label for="type">Transaction Type:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" selected disabled hidden>Select Type</option>
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                        <option value="Equity">Equity</option>
                    </select>
                    <input type="hidden" name="transaction_name" value= "Deposit" />
                </div>
 
                <div class="form-group">
                    <label for="account_id">Account Classification:</label>
                    <select class="form-control" id="account_id" name="account_id" required>
                        <option value="" disabled selected>Select Account Type</option>
                        @foreach ($accounts->unique('description') as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->description  }}</option>
                        @endforeach
                    </select> 
                </div>
                {{-- <div class="form-group"  id="to_account_group" style="display: none;">
                    <label for="to_account_id">Account Transfered To:</label>
                    <select class="form-control" id="to_account_id" name="to_account_id">
                        <option value="" disabled selected>Select Account Type</option>
                        @foreach ($accounts->unique('category') as $account)
                            <option value="{{ $account->id }}">{{ $account->category }}</option>
                        @endforeach
                    </select> 
                </div> --}}
                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="date">Transaction Date:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="amount">Transaction Amount:</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="source_group" style="display: none;">
                   <div class="col">
                        <label for="source">Source of Fund:</label>
                        <select class="form-control" id="source" name="source">
                            <option value="" selected disabled hidden>Select Source of Fund</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Payment">Payment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" selected disabled hidden>Select Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                            <option value="Paid In Part">Paid In Part</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Details:</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>

                <br>

                <div class="align-middle">
                    <button type="submit" class="btn btn-success">Create Deposit</button>
                    <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                    <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to reset form fields
    function resetForm() {
        document.getElementById("account_id").value = "";
        document.getElementById("date").value = "";
        document.getElementById("type").value = "";
        // document.getElementById("transaction_name").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("description").value = "";
        document.getElementById("source").value = "";
        document.getElementById("status").value = "";
        // document.getElementById("to_account_id").value = "";
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    //     $('#type').change(function() {
    //         var selectedType = $(this).val();
    //         if (selectedType) {
    //             $.ajax({
    //                 url: '/get-account-classifications', // Update with your route or endpoint
    //                 method: 'GET',
    //                 data: { selectedType: selectedType },
    //                 success: function(response) {
    //                     $('#account_id').html(response);
    //                 },
    //                 error: function(xhr) {
    //                     console.log(xhr.responseText);
    //                 }
    //             });
    //         } else {
    //             $('#account_id').html('<option value="" disabled selected>Select Account Type</option>');
    //         }
    //     });

        $('#transaction_name').change(function() {
            var selectedTransaction = $(this).val();
            if (selectedTransaction === 'Transfer') {
                $('#to_account_group').show();
            } else {
                $('#to_account_group').hide();
            }
        });

        $('#transaction_name').change(function() {
            var selectedTransaction = $(this).val();
            if (selectedTransaction === 'Expenditure' || selectedTransaction === 'Cash Payment' || selectedTransaction === 'Others') {
                $('#source_group').show();
            } else {
                $('#source_group').hide();
            }
        });
    });
</script>

@endsection
