@extends('layouts.app')

@section('content')
    <div class="row" style="color: #575757; font-style: bold; font-size: 3rem;">
        <div class="col-md-8">Sales List</div>
        <div class="col-md-4">               
            <div style="float:right;"> <a class="btn btn-success" href="{{ route('sales.create') }}">New Sales</a> </div>
        </div>
    </div>
    
    <br>

    <table class="table table-css table-bordered table-hover"> 
            
        <thead class="thead-dark align-middle">
            <tr>
                <th width="10%">Bill No.</th>
                <th width="15%">Customer</th>
                <th width="15%">Stocks Sold</th>
                <th width="10%">Quantity Sold</th>
                <th width="10%">Total Sold Price</th>
                <th width="15%">Date</th>
                <th width="25%">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td class="align-middle"> <p>{{ $sale->id }}</p> </td>
                <td class=""> {{ $sale->name }} <br> <small style="color: #909494">Ph No : {{ $sale->phone }}</small> </td>
                <td class="align-middle">@foreach ($sale->items as $item) {{ $item->stock->name }} <br> @endforeach</td>
                <td class="align-middle">@foreach ($sale->items as $item) {{ $item->quantity }} <br> @endforeach</td>     
                <td class="align-middle">{{ $defaultCurrency }}{{ number_format($sale->totalPrice, 2) }}</td>
                <td class="align-middle">{{ $sale->created_at->format('Y-m-d') }}</td>
                <td class="align-middle"> <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary btn-sm">View Bill</a> <a href="{{ route('sales.destroy', $sale->id) }}" class="btn btn-danger btn-sm">Delete Bill</a> </td>
            </tr>


        @endforeach
        
        </tbody>
    </table>

    <div class="align-middle">
        <!-- Pagination links here -->
    </div>
@endsection
