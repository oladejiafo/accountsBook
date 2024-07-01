@extends('layouts.app')

@section('title', 'Show Return')

@section('content')

<div class="titles" style="color:#575757; font-weight: bold; border-bottom: 1px solid white;">Return Details</div>

<br>

<div class="panel panel-default">
    <div class="panel-heading panel-heading-text">Customer Details</div>
    <div class="panel-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="customer_name" class="panel-body-text">Customer:</label>
                <input type="text" class="form-control" id="customer_name" value="{{ $return->customer->name }}" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="return_date" class="panel-body-text">Return date:</label>
                <input type="date" class="form-control" id="return_date" value="{{ $return->return_date }}" readonly>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading panel-heading-text">Returned Items</div>
    <div class="panel-body">
        @foreach($return->returnedProducts as $index => $item)
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="product_{{ $index }}" class="panel-body-text">Product:</label>
                    <input type="text" class="form-control" id="product_{{ $index }}" value="{{ $item->product->name }}" readonly>
                </div>
                <div class="form-group col-md-2">
                    <label for="quantity_{{ $index }}" class="panel-body-text">Quantity:</label>
                    <input type="number" class="form-control" id="quantity_{{ $index }}" value="{{ $item->quantity }}" readonly>
                </div>
                <div class="form-group col-md-2">
                    <label for="price_{{ $index }}" class="panel-body-text">Total Price:</label>
                    <input type="number" class="form-control" id="price_{{ $index }}" value="{{ $item->price }}" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label for="condition_{{ $index }}" class="panel-body-text">Condition:</label>
                    <input type="text" class="form-control" id="condition_{{ $index }}" value="{{ $item->condition }}" readonly>
                </div>
                <div class="form-group col-md-12">
                    <label for="reason_{{ $index }}" class="panel-body-text">Reason:</label>
                    <textarea class="form-control" id="reason_{{ $index }}" rows="2" readonly>{{ $item->reason }}</textarea>
                </div>
            </div>
            <hr>
        @endforeach
    </div>
</div>

<div class="align-middle mt-3">
    <a href="{{ route('returns.edit', $return->id) }}" class="btn btn-lg btn-primary mr-2">Edit</a>
    <form action="{{ route('returns.destroy', $return->id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-lg btn-danger mr-2" onclick="return confirm('Are you sure you want to delete this return?')">Delete</button>
    </form>
    <a href="{{ route('returns.index') }}" class="btn btn-lg btn-secondary">Back to Returns</a>
</div>


@endsection
