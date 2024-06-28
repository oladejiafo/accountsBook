@extends('layouts.app')
@section('title', 'Sales List')
@section('content')
    <div class="row titles" style="color: #575757; font-style: bold; ">
        <div class="col-8">Sales List</div>
        <div class="col-4">               
            <div style="float:right;"> <a class="btn btn-success" href="{{ route('sales.create') }}">New Sales</a> </div>
        </div>
    </div>
    
    <br>
    <div class="table-responsive">
    <table class="table table-css table-bordered table-hover"> 
        <thead class="thead-light align-middle">
            <tr>
                <th width="8%">Bill No.</th>
                <th width="12%">Date</th>
                <th width="15%">Customer</th>
                <th width="15%">Stocks Sold</th>
                <th width="10%">Quantity Sold</th>
                <th width="10%">Total Price</th>
                <th width="10%">Payment Status</th>
                <th width="20%"></th>
            </tr>
        </thead>

        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td class="align-middle"> <p>{{ $sale->id }}</p> </td>
                <td class="align-middle">{{ $sale->created_at->format('Y-m-d') }}</td>
                <td class=""> {{ $sale->name }} <br> <small style="color: #909494">Ph No : {{ $sale->phone }}</small> </td>
                <td class="align-middle">@foreach ($sale->items as $item) {{ $item->stock->name }} <br> @endforeach</td>
                <td class="align-middle">@foreach ($sale->items as $item) {{ $item->quantity }} <br> @endforeach</td>     
                <td class="align-middle">{{ $defaultCurrency }}{{ number_format($sale->totalPrice, 2) }}</td>
                <td class="align-middle"> <p>{{ $sale->payment_status }}</p> </td>
                <td class="align-middle"> 
                    @if ($sale->payment_status === 'PENDING')
                        <a href="{{ route('payments.create', $sale->id) }}" class="btn btn-info btn-sm">Pay</a> 
                    @endif
                    @if ($sale->payment_status === 'PAID')
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-success btn-sm">View</a> 
                    @endif
                    <!-- Add the edit link here -->
                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    <a href="{{ route('sales.destroy', $sale->id) }}" class="btn btn-danger btn-sm">Delete</a> 
                </td>
                
            </tr>
            @endforeach    
        </tbody>        
    </table>
    </div>

    @if ($sales->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3  d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('sales.index') }}" class="form-inline">
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
                {{ $sales->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>                
    @endif
@endsection
