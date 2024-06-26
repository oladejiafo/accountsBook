<!-- resources/views/reconciliation/index.blade.php -->

@extends('layouts.app')

@section('title', 'Reconciliation')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; ">Reconciliation</h1>
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
                        {{-- <option value=1>General Transactions</option>
                        <option value=2>Payments</option>
                        <option value=3>Deposits</option>
                        <!-- Populate options with bank transactions --> --}}
                        @foreach ($bankTransactions as $bankTransaction)
                            <option value="{{ $bankTransaction->id }}">{{ $bankTransaction->description ?: $bankTransaction->type }} - {{ $bankTransaction->amount }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="accounting_transaction_id">Select Accounting Transaction:</label>
                    <select class="form-control" id="accounting_transaction_id" name="accounting_transaction_id" required>
                        <!-- Populate options with accounting transactions -->
                        @foreach ($accountingTransactions as $accountingTransaction)
                            <option value="{{ $accountingTransaction->id }}">{{ $accountingTransaction->description ?: $accountingTransaction->transaction_name }} - {{ $accountingTransaction->amount }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="float-right mt-2 mb-2"><button type="submit" class="btn btn-success">Match Transactions</button></div>
            </form>
            

            <!-- Display transactions from various tables -->
            <h3>Transactions</h3>
            <div class="table-responsive">
                <table class="table table-css table-bordered table-hover ">
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

                        {{-- @foreach ($deposits as $deposit)
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
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination links -->
            @if ($transactions->isNotEmpty())
            <div class="row mb-3">
                <div class="col-md-3  d-flex align-items-center">
                    <form id="perPageForm" method="GET" action="{{ route('reconciliation.index') }}" class="form-inline">
                        <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                        <select name="per_page" id="per_page" class="form-control" style="width: 65px" onchange="document.getElementById('perPageForm').submit();">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>
                <div class="col-md-9 d-flex justify-content-end">
                    <div class="pagination">
                        {{ $transactions->appends(['per_page' => request('per_page')])->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
