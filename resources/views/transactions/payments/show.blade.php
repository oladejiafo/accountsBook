@extends('layouts.app')

@section('title', 'Show Payment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold;">Payment Details</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="customer_name" class="col-md-4 col-form-label text-md-right">Customer</label>
                        <div class="col-md-6">
                            <input id="customer_name" type="text" class="form-control" value="{{ $payment->customer->name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="stock_name" class="col-md-4 col-form-label text-md-right">Stock</label>
                        <div class="col-md-6">
                            <input id="stock_name" type="text" class="form-control" value="{{ $payment->stock->name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payable_amount" class="col-md-4 col-form-label text-md-right">Payable Amount</label>
                        <div class="col-md-6">
                            <input id="payable_amount" type="number" class="form-control" value="{{ $payment->payable_amount }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="paid_amount" class="col-md-4 col-form-label text-md-right">Paid Amount</label>
                        <div class="col-md-6">
                            <input id="paid_amount" type="number" class="form-control" value="{{ $payment->paid_amount }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="remaining_amount" class="col-md-4 col-form-label text-md-right">Remaining Amount</label>
                        <div class="col-md-6">
                            <input id="remaining_amount" type="number" class="form-control" value="{{ $payment->remaining_amount }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payment_date" class="col-md-4 col-form-label text-md-right">Payment Date</label>
                        <div class="col-md-6">
                            <input id="payment_date" type="date" class="form-control" value="{{ $payment->payment_date }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payment_method" class="col-md-4 col-form-label text-md-right">Payment Method</label>
                        <div class="col-md-6">
                            <input id="payment_method" type="text" class="form-control" value="{{ $payment->payment_method }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payment_type" class="col-md-4 col-form-label text-md-right">Payment Type</label>
                        <div class="col-md-6">
                            <input id="payment_type" type="text" class="form-control" value="{{ $payment->payment_type }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bank_name" class="col-md-4 col-form-label text-md-right">Bank</label>
                        <div class="col-md-6">
                            <input id="bank_name" type="text" class="form-control" value="{{ $payment->bank->name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bank_reference_number" class="col-md-4 col-form-label text-md-right">Bank Reference Number</label>
                        <div class="col-md-6">
                            <input id="bank_reference_number" type="text" class="form-control" value="{{ $payment->bank_reference_number }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="invoice_number" class="col-md-4 col-form-label text-md-right">Invoice Number</label>
                        <div class="col-md-6">
                            <input id="invoice_number" type="text" class="form-control" value="{{ $payment->invoice_number }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="invoice_id" class="col-md-4 col-form-label text-md-right">Invoice ID</label>
                        <div class="col-md-6">
                            <input id="invoice_id" type="text" class="form-control" value="{{ $payment->invoice_id }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="payment_verified_by_cfo" class="col-md-4 col-form-label text-md-right">Payment Verified by CFO</label>
                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="payment_verified_by_cfo" {{ $payment->payment_verified_by_cfo ? 'checked' : '' }} disabled>
                                <label class="custom-control-label" for="payment_verified_by_cfo"></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="remark" class="col-md-4 col-form-label text-md-right">Remark</label>
                        <div class="col-md-6">
                            <textarea id="remark" class="form-control" readonly>{{ $payment->remark }}</textarea>
                        </div>
                    </div>

                    <div class="align-middle">
                        <br>
                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                        </form>
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
