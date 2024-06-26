@extends('layouts.app')

@section('title', 'Tax Transactions')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold;">Tax Transactions</div>
                    <p class="text-muted">Track and manage all tax-related transactions.</p>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <a href="{{ route('tax-transactions.create', ['tab' => 'Business']) }}"
                            class="btn btn-success mb-3 ml-auto justify-content-end" style="float:right;">Create Tax
                            Transaction</a>

                        <div class="table-responsive">
                            <table class="table table-css table-bordered table-hover">
                                <thead class="thead-dark align-middle">
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th>Amount</th>
                                        <th>Transaction Type</th>
                                        <th>Tax Code</th>
                                        {{-- <th>Tax Category ID</th> --}}
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($taxTransactions as $taxTransaction)
                                        <tr>
                                            {{-- <td>{{ $taxTransaction->id }}</td> --}}
                                            <td>{{ $taxTransaction->amount }}</td>
                                            <td>{{ $taxTransaction->transaction_type }}</td>
                                            <td>{{ optional($taxTransaction->taxCode)->name }}</td>
                                            {{-- <td>{{ $taxTransaction->taxCategory->name }}</td> --}}
                                            <td class="align-middle">
                                                {{-- <a href="{{ route('tax-transactions.show', $taxTransaction->id) }}" class="btn btn-primary">View</a> --}}
                                                <a href="{{ route('tax-transactions.edit', $taxTransaction->id) }}"
                                                    class="btn btn-info">Edit</a>
                                                <form action="{{ route('tax-transactions.destroy', $taxTransaction->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this tax transaction?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No tax transactions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($taxTransactions->isNotEmpty())
                            <div class="row mb-3">
                                <div class="col-md-3  d-flex align-items-center">
                                    <form id="perPageForm" method="GET" action="{{ route('tax-transactions.index') }}" class="form-inline">
                                        <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                                        <select name="per_page" id="per_page" class="form-control" style="width: 65px" onchange="document.getElementById('perPageForm').submit();">
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="75" {{ request('per_page') == 75 ? 'selected' : '' }}>75</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </form>
                                </div>
                                <div class="col-md-9 d-flex justify-content-end">
                                    <div class="pagination">
                                        {{ $taxTransactions->appends(['per_page' => request('per_page')])->links() }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
