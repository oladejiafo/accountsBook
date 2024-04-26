<!-- resources/views/reconciliation/index.blade.php -->

@extends('layouts.app')

@section('title', 'Reconciliation')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Reconciliation</h1>
        </div>
        <div class="card-body">
            <!-- Display success message if any -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Form for matching transactions -->
            <form action="{{ route('reconciliation.match') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="bank_transaction_id">Select Bank Transaction:</label>
                    <select class="form-control" id="bank_transaction_id" name="bank_transaction_id" required>
                        <option>General Transactions</option>
                        <option>Payments</option>
                        <option>Deposits</option>
                        <!-- Populate options with bank transactions -->
                        {{-- @foreach ($bankTransactions as $bankTransaction)
                            <option value="{{ $bankTransaction->id }}">{{ $bankTransaction->description }} - {{ $bankTransaction->amount }}</option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="form-group">
                    <label for="accounting_transaction_id">Select Accounting Transaction:</label>
                    <select class="form-control" id="accounting_transaction_id" name="accounting_transaction_id" required>
                        <!-- Populate options with accounting transactions -->
                        @foreach ($accountingTransactions as $accountingTransaction)
                            <option value="{{ $accountingTransaction->id }}">{{ $accountingTransaction->description }} - {{ $accountingTransaction->amount }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Match Transactions</button>
            </form>
            

            <!-- Display transactions from various tables -->
            <h3>Transactions</h3>
            <div class="table-responsive">
                <table class="table table-css table-bordered table-hover">
                    <thead class="thead-dark align-middle">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Matched with Bank Feed</th>
                            <!-- Add other table headers as needed -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through transactions and compare with bank feed -->
                        @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if ($transaction->is_matched)
                                    <span class="badge badge-success">Matched</span>
                                @else
                                    <span class="badge badge-danger">Not Matched</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @foreach ($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->date }}</td>
                            <td>{{ $deposit->type }}</td>
                            <td>{{ $deposit->amount }}</td>
                            <td>{{ $deposit->description }}</td>
                            <td>
                                @if ($deposit->is_matched)
                                    <span class="badge badge-success">Matched</span>
                                @else
                                    <span class="badge badge-danger">Not Matched</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
