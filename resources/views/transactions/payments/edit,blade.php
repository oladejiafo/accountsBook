@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Edit Payment</div>

            <div class="card-body">
                <form method="POST" action="{{ route('payments.update', $payment->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label for="amount" class="col-md-4 col-form-label text-md-right">Amount</label>
                        <div class="col-md-6">
                            <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ $payment->amount }}" required>
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payment_date" class="col-md-4 col-form-label text-md-right">Payment Date</label>
                        <div class="col-md-6">
                            <input id="payment_date" type="date" class="form-control @error('payment_date') is-invalid @enderror" name="payment_date" value="{{ $payment->payment_date }}" required>
                            @error('payment_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                   <!-- Add more fields here -->
                    <div class="form-group row">
                        <label for="payment_method" class="col-md-4 col-form-label text-md-right">Payment Method</label>
                        <div class="col-md-6">
                            <input id="payment_method" type="text" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" value="{{ $payment->payment_method }}" required>
                            @error('payment_method')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="stock_id" class="col-md-4 col-form-label text-md-right">Stock</label>
                        <div class="col-md-6">
                            <select id="stock_id" class="form-control @error('stock_id') is-invalid @enderror" name="stock_id" required>
                                <option value="">Select Stock</option>
                                <!-- Iterate through stocks and display as options -->
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->id }}" {{ $payment->stock_id == $stock->id ? 'selected' : '' }}>{{ $stock->name }}</option>
                                @endforeach
                            </select>
                            @error('stock_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">Update Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
