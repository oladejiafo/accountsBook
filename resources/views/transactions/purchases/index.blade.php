@extends('layouts.app')

@section('title', 'Purchases List')

@section('content')
<div class="container">
    <div class="row mb-3 card-header">
        <div class="col-md-6">
            <h2 style="color: #575757; font-weight: bold;">Purchases List</h2>
        </div>
        <div class="col-md-6">
            <div class="text-right">
                <a class="btn btn-success" href="{{ route('select-supplier') }}">New Incoming Stock</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-css table-bordered">
            <thead class="thead-light">
                <tr>
                    <th width="10%">Bill No.</th>
                    <th width="15%">Supplier</th>
                    <th width="15%">Stocks Purchased</th>
                    <th width="10%">Quantity Purchased</th>
                    <th width="15%">Total Purchased Price</th>
                    <th width="10%">Purchased Date</th>
                    <th width="25%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>
                        @if ($purchase->supplier->is_deleted)
                        {{ $purchase->supplier }}<br>
                        @else
                        <a href="{{ route('supplier', $purchase->supplier->id) }}">{{ $purchase->supplier->name }}</a><br>
                        @endif
                        <small style="color: #909494">Ph No : {{ $purchase->supplier->phone }}</small>
                    </td>
                    <td>
                        @foreach ($purchase->items as $item)
                        {{ $item->stock->name }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($purchase->items as $item)
                        {{ $item->quantity }}<br>
                        @endforeach
                    </td>
                    <td>{{ $defaultCurrency }} {{ number_format($purchase->items->sum('totalprice'), 2) }}</td>
                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-secondary btn-sm">View
                            Bill</a>
                        <a href="{{ route('purchase.destroy', $purchase->id) }}"
                            class="btn btn-danger btn-sm">Delete Bill</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="color: #575757; font-size: 1.5rem;">
                        No records found. Please try adding some.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($purchases->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('purchase.index') }}" class="form-inline">
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
                {{ $purchases->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
