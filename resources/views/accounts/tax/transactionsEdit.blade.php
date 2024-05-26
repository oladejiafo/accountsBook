@extends('layouts.app')
@section('title', 'Edit Tax Transaction')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; ">Edit Tax Transaction</div>
                    <p class="text-muted">Track and manage all tax-related transactions.</p>
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

                        <form method="POST" action="{{ route('tax-transactions.update', $taxTransaction->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="amount">Amount:</label>
                                <input type="text" class="form-control" id="amount" name="amount" value="{{ old('amount', $taxTransaction->amount) }}">
                            </div>

                            <div class="form-group">
                                <label for="transaction_type">Transaction Type:</label>
                                <select class="form-control" id="transaction_type" name="transaction_type">
                                    <option value="">Select Transaction Type</option>
                                    @foreach($transactionTypes as $type)
                                        <option value="{{ $type->name }}" @if($type->name == $taxTransaction->transaction_type) selected @endif>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tax_code_id">Tax Code:</label>
                                <select class="form-control" id="tax_code_id" name="tax_code_id">
                                    <option value="">Select Tax Code</option>
                                    @foreach($taxCodes as $taxCode)
                                        <option value="{{ $taxCode->id }}" @if($taxCode->id == $taxTransaction->tax_code_id) selected @endif>{{ $taxCode->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- <div class="form-group">
                                <label for="tax_category_id">Tax Category:</label>
                                <select class="form-control" id="tax_category_id" name="tax_category_id">
                                    <option value="">Select Tax Category</option>
                                    @foreach($taxCategories as $taxCategory)
                                        <option value="{{ $taxCategory->id }}" @if($taxCategory->id == $taxTransaction->tax_category_id) selected @endif>{{ $taxCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>                             --}}

                            <!-- Add more fields as needed -->
                            <div class="align-middle">
                                <button type="submit" class="btn btn-info">Update Tax Transaction</button>
                                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                                <a href="{{ route('tax-transactions.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
