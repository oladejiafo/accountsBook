<!-- resources/views/transactions/returns/edit.blade.php -->

@extends('layouts.app')

@section('title', 'Edit Return')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edit Return</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('returns.update', $return->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="customer_id">Customer:</label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id == $return->customer_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="return_date">Return Date:</label>
                            <input type="date" name="return_date" id="return_date" class="form-control" value="{{ $return->return_date }}" required>
                        </div>

                        <!-- Returned Items -->
                        <div class="panel panel-default">
                            <div class="panel-heading panel-heading-text">Returned Items</div>
                            <div class="panel-body">
                                <div id="returnItems">
                                    @foreach($return->returnedProducts as $index => $item)
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="product_id" class="panel-body-text">Product:</label>
                                            <select name="items[{{ $index }}][product_id]" class="form-control" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ $product->id == $item->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item->product_name }}" required>
                                        </div>


                                        <div class="form-group col-md-2">
                                            <label for="quantity" class="panel-body-text">Quantity:</label>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control" min="1" value="{{ $item->quantity }}" required>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="price" class="panel-body-text">Total Price:</label>
                                            <input type="number" name="price" id="price" class="form-control" min="1" value="{{ $return->refund_amount }}" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="condition" class="panel-body-text">Condition:</label>
                                            <input type="text" name="items[{{ $index }}][condition]" class="form-control" value="{{ $item->condition }}" required>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="reason_for_return" class="panel-body-text">Reason:</label>
                                            <textarea name="reason" id="reason_for_return" class="form-control" rows="2">{{ $return->reason_for_return }}</textarea>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="text-right">
                                    <a href="#" class="add-return-item">+ Add Item</a>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Return</button>
                        <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
