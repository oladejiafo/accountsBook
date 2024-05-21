@extends('layouts.app')

@section('title', $supplier->name . "'s Profile")

@section('content')
<div class="content-section">
    <div class="media">
        <div class="media-body">
            <h2 style="color:#575757;" class="account-heading">&nbsp;{{ $supplier->name }}</h2>
            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-info" style="float: right;">Edit Details</a>
            <div class="row">
                <div class="col-md-6">
                    <p class="fal">
                        <b>Contact &nbsp; &nbsp;&nbsp; :</b> {{ $supplier->phone }} <br>
                        <b>Email Id &nbsp;&nbsp;&nbsp;&nbsp; :</b> {{ $supplier->email }} <br>
                        <b>GSTIN No &nbsp; :</b> {{ $supplier->gstin }} <br>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="fal">
                        <b>Address:</b> <br> {{ $supplier->address }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-css table-hover table-bordered table-responsive">
    <br>
    <thead class="thead-dark align-middle">
        <tr>
            <th width="10%">Bill No.</th>
            <th width="15%">Stocks</th>
            <th width="15%">Quantity</th>
            <th width="15%">Total Purchased Price</th>
            <th width="15%">Purchased Date</th>
            <th width="30%">Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($supplier->purchases as $purchase)
        <tr>
            <td class="align-middle"> <p>{{ $purchase->id }}</p> </td>
            <td class="align-middle">
                @foreach ($purchase->items as $item)
                    {{ $item->stock->name }} <br>
                @endforeach
            </td>
            <td class="align-middle">
                @foreach ($purchase->items as $item)
                    {{ $item->quantity }} <br>
                @endforeach
            </td>     
            <td class="align-middle">${{ $purchase->totalprice }}</td>
            <td class="align-middle">{{ $purchase->created_at->toDateString() }}</td>
            <td class="align-middle">
                <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-secondary">View Bill</a>
                <a href="{{ route('purchase.destroy', $purchase->id) }}" class="btn btn-danger">Delete Bill</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="align-middle">
    {{-- {{ $supplier->purchases->links() }} --}}
    {{ $purchases->links() }}
</div>
@endsection
