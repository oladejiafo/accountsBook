@extends('layouts.app')

@section('title', 'Purchases List')

@section('content')

<div class="row" style="color: #575757; font-style: bold; ">
    <div class="col-6 titles">Purchases List</div>
    <div class="col-6">
        <div style="float:right;">
            <a class="btn btn-success" href="{{ route('select-supplier') }}">New Incoming Stock</a>
        </div>
    </div>
</div>

<br>

<table class="table table-css table-hover table-bordered table-responsive">
    <thead class="thead-dark align-middle">
        <tr>
            <th width="10%">Bill No.</th>
            <th width="15%">Supplier</th>
            <th width="15%">Stocks Purchased</th>
            <th width="10%">Quantity Purchased</th>
            <th width="15%">Total Purchased Price</th>
            <th width="10%">Purchased Date</th>
            <th width="25%">Actions</th>
        </tr>
    </thead>

    @if ($purchases->count() > 0)
    <tbody>
        @foreach ($purchases as $purchase)
        <tr>
            <td class="align-middle"><p>{{ $purchase->id }}</p></td>
            <td class="">
                @if ($purchase->supplier->is_deleted)
                {{ $purchase->supplier }}<br>
                @else
                <a href="{{ route('supplier', $purchase->supplier->id) }}">{{ $purchase->supplier->name }}</a><br>
                @endif
                <small style="color: #909494">Ph No : {{ $purchase->supplier->phone }}</small>
            </td>
            <td class="align-middle">
                @foreach ($purchase->items as $item)
                {{ $item->stock->name }}<br>
                @endforeach
            </td>
            <td class="align-middle">
                @foreach ($purchase->items as $item)
                {{ $item->quantity }}<br>
                @endforeach
            </td>
            <td class="align-middle">{{ $defaultCurrency }} {{ number_format($item->totalprice, 2) }}</td>
            <td class="align-middle">{{ $purchase->created_at->format('Y-m-d') }}</td>
            <td class="align-middle">
                <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-secondary btn-sm">View Bill</a>
                <a href="{{ route('purchase.destroy', $purchase->id) }}" class="btn btn-danger btn-sm">Delete Bill</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>

    <div class="align-middle">
        @if ($purchases->lastPage() > 1)
        @if ($purchases->currentPage() > 1)
        <a class="btn btn-outline-info mb-4" href="{{ $purchases->url(1) }}">First</a>
        <a class="btn btn-outline-info mb-4" href="{{ $purchases->previousPageUrl() }}">Previous</a>
        @endif

        @for ($i = max($purchases->currentPage() - 3, 1); $i <= min($purchases->currentPage() + 3, $purchases->lastPage()); $i++)
            @if ($i == $purchases->currentPage())
            <a class="btn btn-info mb-4" href="{{ $purchases->url($i) }}">{{ $i }}</a>
            @else
            <a class="btn btn-outline-info mb-4" href="{{ $purchases->url($i) }}">{{ $i }}</a>
            @endif
        @endfor

        @if ($purchases->currentPage() < $purchases->lastPage())
        <a class="btn btn-outline-info mb-4" href="{{ $purchases->nextPageUrl() }}">Next</a>
        <a class="btn btn-outline-info mb-4" href="{{ $purchases->url($purchases->lastPage()) }}">Last</a>
        @endif
        @endif
    </div>
    @else

    </tbody>
</table>

<br><br><br><br><br><br><br><br>
<div style="color: #575757; font-style: bold; font-size: 1.5rem; text-align: center;">The records are empty. Please try adding some.</div>

@endif

@endsection
