@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">{{ isset($saleId) ? 'Create Payment for Sale' : 'Create New Payment' }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf

                        @if(isset($saleId))
                        <!-- Hidden field to store the sale ID -->
                        <input type="hidden" name="sale_id" value="{{ $saleId }}">
                        @endif

                        <div class="form-group row">
                            <label for="customer_id" class="col-md-4 col-form-label text-md-right">Customer</label>
                            <div class="col-md-6">
                                <select id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    <!-- Iterate through customers and display as options -->
                                </select>
                                @error('customer_id')
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
                                        <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>{{ $stock->name }}</option>
                                    @endforeach
                                </select>
                                @error('stock_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>                  

                        <div class="form-group row">
                            <label for="amount" class="col-md-4 col-form-label text-md-right">Amount</label>
                            <div class="col-md-6">
                                <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
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
                                <input id="payment_date" type="date" class="form-control @error('payment_date') is-invalid @enderror" name="payment_date" value="{{ old('payment_date') }}" required>
                                @error('payment_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="payment_method" class="col-md-4 col-form-label text-md-right">Payment Method</label>
                            <div class="col-md-6">
                                <select id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <!-- Add more payment methods as needed -->
                                </select>
                                @error('payment_method')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description') }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="recipient_type" class="col-md-4 col-form-label text-md-right">Recipient Type</label>
                            <div class="col-md-6">
                                <input id="recipient_type" type="text" class="form-control @error('recipient_type') is-invalid @enderror" name="recipient_type" value="{{ old('recipient_type') }}" required>
                                @error('recipient_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Payment') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for change event on stock dropdown
        document.getElementById('stock_id').addEventListener('change', function() {
            var stockId = this.value;
            // Make an AJAX request to fetch stock details
            axios.get('{{ route("stocks.details", ["stock" => ":stockId"]) }}'.replace(':stockId', stockId))
                .then(function(response) {
                    // Update the amount field with the retrieved amount
                    document.getElementById('amount').value = response.data.amount;
                })
                .catch(function(error) {
                    console.error('Error fetching stock details:', error);
                });
        });
    });
</script>
    


{{-- <script>
    $(document).ready(function () {
        $('#stock_id').change(function () {
            var stockId = $(this).val();
            if (stockId) {
                // Ajax request to fetch stock details
                $.ajax({
                    url: '/get-stock-details/' + stockId, // Replace this with your route to fetch stock details
                    type: 'GET',
                    success: function (response) {
                        $('#amount').val(response.price); // Update the amount field with the stock price
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });
</script> --}}

@endsection
