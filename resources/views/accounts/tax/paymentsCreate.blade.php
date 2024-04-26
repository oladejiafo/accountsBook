@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create Tax Payment</div>
                    <p class="text-muted">View and manage tax payments made by your business.</p>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('tax-payments.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="amount">Amount:</label>
                                <input type="text" class="form-control" id="amount" name="amount" value="{{ old('amount') }}">
                            </div>

                            <div class="form-group">
                                <label for="payment_date">Payment Date:</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="tax_type">Tax Type:</label>
                                <input type="text" class="form-control" id="tax_type" name="tax_type" value="{{ old('tax_type') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="reference">Reference:</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') }}">
                            </div>

                            <!-- Add more fields as needed -->

                            <div class="align-middle">
                                <button type="submit" class="btn btn-success">Create Tax Payment</button>
                                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                                <a href="{{ route('tax-payments.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
