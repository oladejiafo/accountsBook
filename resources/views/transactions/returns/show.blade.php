<!-- resources/views/returns/show.blade.php -->

@extends('layouts.app')

@section('title', 'Sales Return')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Returns</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('returns.process') }}" method="post">
                @csrf
                <!-- Customer Information -->
                <div class="form-group row">
                    <label for="customer_name" class="col-sm-3 col-form-label panel-body-text">Customer Name:</label>
                    <div class="col-sm-9">
                        <input type="text" name="customer_name" id="customer_name" class="form-control" autocomplete="on" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="customer_email" class="col-sm-3 col-form-label">Customer Email:</label>
                    <div class="col-sm-9">
                        <input type="email" name="customer_email" id="customer_email" class="form-control" readonly>
                    </div>
                </div>
    
                <!-- Transaction Information -->
                <div class="form-group row">
                    <label for="customer_transactions" class="col-sm-3 col-form-label">Customer Transactions:</label>
                    
                    <div class="col-sm-9">
                        <ul id="customer_transactions" class="list-group">
                            <!-- Transactions will be listed here -->
                        </ul>
                    </div>
                </div>

                <!-- Return Transaction Fields -->
                <div class="form-group row">
                    <label for="reason_for_return" class="col-sm-3 col-form-label">Reason for Return:</label>
                    <div class="col-sm-9">
                        <textarea name="reason_for_return" id="reason_for_return" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="condition" class="col-sm-3 col-form-label">Condition of Goods:</label>
                    <div class="col-sm-9">
                        <select name="condition" id="condition" class="form-control">
                            <option value="">Select Condition</option>
                            <option value="Resalable">Resalable</option>
                            <option value="Used">Used</option>
                            <option value="Damaged">Damaged</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="refund_amount" class="col-sm-3 col-form-label">Refund Amount:</label>
                    <div class="col-sm-9">
                        <input type="number" name="refund_amount" id="refund_amount" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="exchange" class="col-sm-3 col-form-label">Refund or Exchange:</label>
                    <div class="col-sm-9">
                        <select name="exchange" id="exchange" class="form-control">
                            <option value="refund">Refund</option>
                            <option value="exchange">Exchange</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exchange" class="col-sm-3 col-form-label">Approval Required:</label>
                    <label class="switch">
                        <input id="approval_required" type="checkbox" class="form-check-input @error('approval_required') is-invalid @enderror" name="approval_required" {{ old('approval_required') ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                
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
                                <th>Reason for Return</th>
                                <th>Return Status</th>
                                <th>Refund Amount</th>
                                <th>Approval Required</th>
                                <th>Exchange</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        
                        <tbody id="returnProducts">
                            <!-- One row visible by default -->
                            <tr class="returned-product">
                                <td><input type="text" name="product_id[]" class="form-control"></td>
                                <td><input type="text" name="name[]" class="form-control"></td>
                                <td><input type="number" name="quantity[]" class="form-control"></td>
                                <td><input type="text" name="condition[]" class="form-control"></td>
                                <td><input type="text" name="reason_for_return[]" class="form-control"></td>
                                <td><input type="text" name="return_status[]" class="form-control"></td>
                                <td><input type="number" name="refund_amount[]" class="form-control"></td>
                                <td><input type="checkbox" name="approval_required[]" value="1"></td>
                                <td>
                                    <select name="exchange[]" class="form-control">
                                        <option value="refund">Refund</option>
                                        <option value="exchange">Exchange</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="form-control btn btn-danger remove-form-row">-</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-success" id="addProduct">Add Product</button>
                <button type="submit" class="btn btn-warning">Process Return</button>
            </form>
        </div>
    </div>

    
    <script type="text/javascript" src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dialogbox.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript"> 
        var custom_alert = new custom_alert();
        $(document).ready(function() {

            $('#customer_name').on('input', function() {
                var customerName = $(this).val();
                if (customerName.length >= 3) {
                    $.ajax({
                        url: '{{ route("fetchCustomerDetails") }}',
                        method: 'GET',
                        data: {
                            name: customerName
                        },
                        success: function(response) {
                            // Update customer details fields
                            updateCustomerDetails(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Handle errors or display a message to the user
                        }
                    });
                }
            });
        });

        $('#addProduct').click(function() {
            $('#returnProducts').append(`
                <tr class="returned-product">
                    <td><input type="text" name="product_id[]" class="form-control"></td>
                    <td><input type="text" name="name[]" class="form-control"></td>
                    <td><input type="number" name="quantity[]" class="form-control"></td>
                    <td><input type="text" name="condition[]" class="form-control"></td>
                    <td><input type="text" name="reason_for_return[]" class="form-control"></td>
                    <td><input type="text" name="return_status[]" class="form-control"></td>
                    <td><input type="number" name="refund_amount[]" class="form-control"></td>
                    <td><input type="checkbox" name="approval_required[]" value="1"></td>
                    <td>
                        <select name="exchange[]" class="form-control">
                            <option value="refund">Refund</option>
                            <option value="exchange">Exchange</option>
                        </select>
                    </td>
                    <td>
                        <!-- Button to remove row -->
                        <button class="form-control btn btn-danger remove-form-row">-</button>
                    </td>
                    <!-- Add more columns as needed -->
                </tr>
            `);
        });

        // Remove button functionality
        $('#returnProducts').on('click', '.remove-form-row', function() {
            // Check if there's more than one row
            if ($('#returnProducts tr.returned-product').length > 1) {
                $(this).closest('tr').remove();
            } else {
                // alert("At least one product must remain.");
                custom_alert.render('Field cannot be deleted');
            }
        });

        // Function to update customer details
        function updateCustomerDetails(customer) {
            $('#customer_email').val(customer.email);
            fetchTransactions(customer.id);
        }

        // Function to fetch transactions for a customer
        function fetchTransactions(customerId) {
            $.ajax({
                url: '{{ route("fetchCustomerTransactions") }}',
                method: 'GET',
                data: {
                    customer_id: customerId
                },
                success: function(response) {
                    // Clear previous transactions
                    $('#customer_transactions').empty();
                    // Display transactions
                    response.transactions.forEach(function(transaction) {
                        $('#customer_transactions').append('<li class="list-group-item">' + transaction + '</li>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Handle errors or display a message to the user
                }
            });
        }
    </script>
@endsection
