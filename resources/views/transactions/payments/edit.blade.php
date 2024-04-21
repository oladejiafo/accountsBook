@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Payment</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="customer_id" class="col-md-4 col-form-label text-md-right">Customer</label>
                            <div class="col-md-6">
                                <select id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $payment->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>                                
                                @error('customer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
     
                        <div class="form-group row">
                            <label for="stock_id" class="col-md-4 col-form-label text-md-right">Stock {{$payment->stock_id}}</label>
                            <div class="col-md-6">
                                <select id="stock_id" class="form-control @error('stock_id') is-invalid @enderror" name="stock_id" required>
                                    <option value="">Select Stock </option>
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

                        <div class="form-group row">
                            <label for="payable_amount" class="col-md-4 col-form-label text-md-right">Payable Amount</label>
                            <div class="col-md-6">
                                <input id="payable_amount" type="number" class="form-control @error('payable_amount') is-invalid @enderror" name="payable_amount" value="{{ $payment->payable_amount }}" required>
                                @error('payable_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="paid_amount" class="col-md-4 col-form-label text-md-right">Paid Amount</label>
                            <div class="col-md-6">
                                <input id="paid_amount" type="number" class="form-control @error('paid_amount') is-invalid @enderror" name="paid_amount" value="{{ $payment->paid_amount }}" required>
                                @error('paid_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="remaining_amount" class="col-md-4 col-form-label text-md-right">Remaining Amount</label>
                            <div class="col-md-6">
                                <input id="remaining_amount" type="number" class="form-control @error('remaining_amount') is-invalid @enderror" name="remaining_amount" value="{{ $payment->remaining_amount }}" required>
                                @error('remaining_amount')
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

                        <div class="form-group row">
                            <label for="payment_method" class="col-md-4 col-form-label text-md-right">Payment Method</label>
                            <div class="col-md-6">
                                <select id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash" {{ $payment->payment_method === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Credit Card" {{ $payment->payment_method === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Bank Transfer" {{ $payment->payment_method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
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
                            <label for="payment_type" class="col-md-4 col-form-label text-md-right">Payment Type</label>
                            <div class="col-md-6">
                                <input id="payment_type" type="text" class="form-control @error('payment_type') is-invalid @enderror" name="payment_type" value="{{ $payment->payment_type }}" required>
                                @error('payment_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bank_id" class="col-md-4 col-form-label text-md-right">Bank</label>
                            <div class="col-md-6">
                                <select id="bank_id" class="form-control @error('bank_id') is-invalid @enderror" name="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ $payment->bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    
                        <div class="form-group row">
                            <label for="bank_reference_number" class="col-md-4 col-form-label text-md-right">Bank Reference Number</label>
                            <div class="col-md-6">
                                <input id="bank_reference_number" type="text" class="form-control @error('bank_reference_number') is-invalid @enderror" name="bank_reference_number" value="{{ $payment->bank_reference_number }}">
                                @error('bank_reference_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="invoice_number" class="col-md-4 col-form-label text-md-right">Invoice Number</label>
                            <div class="col-md-6">
                                <input id="invoice_number" type="text" class="form-control @error('invoice_number') is-invalid @enderror" name="invoice_number" value="{{ $payment->invoice_number }}" >
                                @error('invoice_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="invoice_id" class="col-md-4 col-form-label text-md-right">Invoice ID</label>
                            <div class="col-md-6">
                                <input id="invoice_id" type="text" class="form-control @error('invoice_id') is-invalid @enderror" name="invoice_id" value="{{ $payment->invoice_id }}">
                                @error('invoice_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label for="payment_verified_by_cfo" class="col-md-4 col-form-label text-md-right">Payment Verified by CFO</label>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-toggle" id="payment_verified_by_cfo" name="payment_verified_by_cfo" data-toggle="button" aria-pressed="{{ $payment->payment_verified_by_cfo ? 'true' : 'false' }}" autocomplete="off">
                                    <div class="handle"></div>
                                </button>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="payment_verified_by_cfo" class="col-md-4 col-form-label text-md-right">Payment Verified by CFO</label>
                            <div class="col-md-6">
                                <label class="switch">
                                    <input id="payment_verified_by_cfo" type="checkbox" class="form-check-input @error('payment_verified_by_cfo') is-invalid @enderror" name="payment_verified_by_cfo" {{ $payment->payment_verified_by_cfo ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                @error('payment_verified_by_cfo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="remark" class="col-md-4 col-form-label text-md-right">Remark</label>
                            <div class="col-md-6">
                                <textarea id="remark" class="form-control @error('remark') is-invalid @enderror" name="remark" required>{{ $payment->remark }}</textarea>
                                @error('remark')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="align-middle">
                            <br>
                            <button type="submit" class="btn btn-success">Update Payment</button>
                            <a href="{{ route('payments.destroy', $payment->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to reset form fields
function resetForm() {
document.getElementById("account_id").value = "";
document.getElementById("date").value = "";
document.getElementById("type").value = "";
document.getElementById("transaction_name").value = "";
document.getElementById("amount").value = "";
document.getElementById("description").value = "";
document.getElementById("source").value = "";
document.getElementById("status").value = "";
document.getElementById("to_account_id").value = "";
}
</script>

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

<script>
    // Add an event listener for checkbox change
    document.getElementById('payment_verified_by_cfo').addEventListener('change', function() {
        // Get the checkbox state
        var isChecked = this.checked;

        // Select the slider element
        var slider = document.querySelector('.slider');

        // Toggle the 'checked' class based on the checkbox state
        if (isChecked) {
            slider.classList.add('checked');
        } else {
            slider.classList.remove('checked');
        }
    });
</script>
@endsection
