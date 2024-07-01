@extends('layouts.app')
@section('title', 'Sales Payments')
@section('content')
<div class="container">
    <div class="card">
            
        <div class="row card-header">
            <div class="col-6"  style="color: #4e4e4e; font-style: bold;">Payments</div>
            <div class="col-6">
                <div style="float:right;" class="d-flex justify-content-end mt-3">
                    <div>
                        <a href="{{ route('payments.create') }}" class="btn btn-success mb-3">Create Payment</a>
                    </div>
                </div>
            </div>
        </div>

        <div style="border-bottom: 1px solid white;"></div>
        <form method="GET" action="{{ route('payments.index') }}">
            <div class="input-group search">
                <input type="text" name="search" class="form-control textinput" placeholder="Search for payments">
                <div class="input-group-append">
                <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
                </div>
            </div>
        </form>
        <br>

        <div class="card-body table-responsive">
            <table class="table table-css table-bordered table-hover">
                <thead class="thead-light align-middle">
                    <tr>
                        <th width="10%">Payment ID</th>
                        <th width="20%">Customer</th>
                        <th width="15%">Paid Amount</th>
                        <th width="15%">Payment Date</th>
                        {{-- <th width="15%">Payment Method</th> --}}
                        <th width="15%">Bank</th>
                        <th width="10%">CFO Verified</th>
                        <th width="25%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                    <tr>
                        <td class="align-middle">{{ $payment->id }}</td>
                        <td class="align-middle">{{ $payment->customer->name }}</td>
                        <td class="align-middle">{{ $payment->paid_amount }}</td>
                        <td class="align-middle">{{ $payment->payment_date }}</td>
                        {{-- <td class="align-middle">{{ $payment->payment_method }}</td> --}}
                        <td class="align-middle">{{ $payment->bank ? $payment->bank->name : 'N/A' }}</td>
                        <td class="align-middle">{{ $payment->payment_verified_by_cfo ? 'Yes' : 'No' }}</td>
                        <td class="align-middle">
                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-info btn-sm">Edit</a>
                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if ($payments->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('payments.index') }}" class="form-inline">
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
                {{ $payments->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
