<!-- resources/views/returns/show.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Returns</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('returns.process') }}" method="post">
                @csrf
                <div class="form-group row">
                    <label for="return_transaction_id" class="col-sm-3 col-form-label">Select Return Transaction:</label>
                    <div class="col-sm-9">
                        <select name="return_transaction_id" id="return_transaction_id" class="form-control">
                            <option value="" disabled selected>Select Return Transaction</option>
                            @foreach ($returnTransactions as $transaction)
                                <option value="{{ $transaction->id }}">{{ $transaction->id }} - {{ $transaction->created_at }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Return Transaction Fields -->
                <div class="form-group row">
                    <label for="reason_for_return" class="col-sm-3 col-form-label">Reason for Return:</label>
                    <div class="col-sm-9">
                        <input type="text" name="reason_for_return" id="reason_for_return" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="return_status" class="col-sm-3 col-form-label">Return Status:</label>
                    <div class="col-sm-9">
                        <input type="text" name="return_status" id="return_status" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="refund_amount" class="col-sm-3 col-form-label">Refund Amount:</label>
                    <div class="col-sm-9">
                        <input type="number" name="refund_amount" id="refund_amount" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="approval_required">Approval Required:</label>
                    <input type="checkbox" id="approval_required" name="approval_required" value="0">
                </div>
                
                <!-- Add more fields as needed -->

                <!-- Return Products Table -->
                <div class="form-group">
                    <label for="return_products">Return Products:</label>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Quantity Returned</th>
                                <th>Condition</th>
                                <!-- Add more columns as needed -->
                            </tr>
                        </thead>
                        <tbody id="returnProducts">
                            <!-- One row visible by default -->
                            <tr class="returned-product">
                                <td><input type="text" name="product_id[]" class="form-control"></td>
                                <td><input type="text" name="name[]" class="form-control"></td>
                                <td><input type="number" name="quantity[]" class="form-control"></td>
                                <td><input type="text" name="condition[]" class="form-control"></td>
                                <!-- Add more columns as needed -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-primary" id="addProduct">Add Product</button>
                <button type="submit" class="btn btn-primary">Process Return</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#addProduct').click(function() {
                $('#returnProducts').append(`
                    <tr class="returned-product">
                        <td><input type="text" name="product_id[]" class="form-control"></td>
                        <td><input type="text" name="name[]" class="form-control"></td>
                        <td><input type="number" name="quantity[]" class="form-control"></td>
                        <td><input type="text" name="condition[]" class="form-control"></td>
                        <!-- Add more columns as needed -->
                    </tr>
                `);
            });
        });
    </script>
@endsection
