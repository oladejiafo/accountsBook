@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Customer</div>

        <div class="card-body">
            <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $customer->name }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $customer->email }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $customer->phone }}" required>
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ $customer->address }}</textarea>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city">City</label>
                        <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ $customer->city }}" required>
                        @error('city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="country">Country</label>
                        <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ $customer->country }}" required>
                        @error('country')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Add other customer fields as necessary -->

                <div class="form-group">
                    <label for="billing_address">Billing Address</label>
                    <textarea id="billing_address" class="form-control @error('billing_address') is-invalid @enderror" name="billing_address">{{ $customer->billing_address }}</textarea>
                    @error('billing_address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="shipping_address">Shipping Address</label>
                    <textarea id="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" name="shipping_address">{{ $customer->shipping_address }}</textarea>
                    @error('shipping_address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="customer_type">Customer Type</label>
                    <select id="customer_type" class="form-control @error('customer_type') is-invalid @enderror" name="customer_type">
                        <option value="individual" {{ $customer->customer_type == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="business" {{ $customer->customer_type == 'business' ? 'selected' : '' }}>Business</option>
                    </select>
                    @error('customer_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ $customer->notes }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_terms">Payment Terms</label>
                    <input id="payment_terms" type="text" class="form-control @error('payment_terms') is-invalid @enderror" name="payment_terms" value="{{ $customer->payment_terms }}">
                    @error('payment_terms')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tax_exempt">Tax Exempt</label><br>
                    <label class="switch">
                        <input id="tax_exempt" type="checkbox" class="form-control @error('tax_exempt') is-invalid @enderror" name="tax_exempt" {{ $customer->tax_exempt ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    @error('tax_exempt')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="align-middle">
                    <br>
                    <button type="submit" class="btn btn-success">Update Customer</button>
                    <a href="{{ route('customers.destroy', $customer->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
