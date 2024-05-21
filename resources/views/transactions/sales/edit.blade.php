@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')

<div class="titles" style="color:#575757; font-style: bold;  border-bottom: 1px solid white;">Edit Sale</div>

<br>

<form method="post" action="{{ route('sales.update', $sale->id) }}">
    @csrf
    @method('PUT')

    <!-- Customer Details -->
    <div class="panel-heading panel-heading-text">Customer Details</div>
    <div class="panel-body">
        <!-- Customer Name -->
        <div class="form-group">
            <label for="name" class="panel-body-text">Customer Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $sale->name }}" required>
        </div>

        <!-- Other Customer Details -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="phone" class="panel-body-text">Phone No:</label>
                <input type="tel" name="phone" id="phone" class="form-control" maxlength="10" pattern="[0-9]{10}" title="Numbers only" value="{{ $sale->phone }}">
            </div>
            <div class="form-group col-md-6">
                <label for="email" class="panel-body-text">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $sale->email }}">
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="panel-body-text">Address:</label>
            <textarea name="address" id="address" class="form-control" rows="4">{{ $sale->address }}</textarea>
        </div>
    </div>

    <br>

<!-- Product Details -->
<div class="panel panel-default">
    <div class="panel-heading panel-heading-text">Product Details</div>
    <div id="stockitem">
        <div class="panel-body">
            <!-- Initial row for product details -->
            <div class="row form-row">
                <!-- Your product fields here, pre-filled with existing data -->
                <div class="form-group col-md-5">
                    <label class="panel-body-text">Stock Name:</label>
                    <select id="stock_name" name="items[0][stock_name]" class="form-control stock_name" required>
                        <option value="">Select Stock Name</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->name }}" data-price="{{ $stock->price }}">{{ $stock->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label class="panel-body-text">Quantity:</label>
                    <input id="quantity" type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" required>
                </div>
                <div class="form-group col-md-2">
                    <label class="panel-body-text">Unit Price:</label>
                    <input type="number" name="items[0][unit_price]" class="form-control unit_price" readonly required>
                </div>
                <div class="form-group col-md-2">
                    <label class="panel-body-text">Total Amount:</label>
                    <input type="number" name="items[0][total_amount]" class="form-control total_amount" readonly required>
                </div>
                <div class="form-group col-md-1">
                    <label class="panel-body-text" style="color: #000">.</label>
                    <!-- Remove button -->
                    <button class="form-control btn btn-danger remove-form-row">-</button>
                </div>
            </div>
            <!-- Add More Button -->
            <div style="text-align: right;">
                <a href="#" class="add-form-row">+ Add More</a>
            </div>
        </div>
    </div>
</div>


    <br>

    <!-- Submit Button -->
    <div class="align-middle">
        <button type="submit" class="btn btn-primary">Update Sale</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

   <!-- Custom JS to add and remove item forms -->
   <script type="text/javascript" src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
   <script type="text/javascript" src="{{ asset('js/dialogbox.js') }}"></script>
   <script type="text/javascript">
       
       
       //creates custom alert object
       var custom_alert = new custom_alert();
       $(document).ready(function() {
            // Function to update unit price and total amount when stock item is selected
            function updateUnitPriceAndTotalAmount(selectElement) {
                var selectedOption = selectElement.find('option:selected');
                var unitPrice = parseFloat(selectedOption.data('price'));
                var quantity = parseFloat(selectElement.closest('.form-row').find('.quantity').val());
                var totalAmount = unitPrice * quantity;
                // Format total amount to display as a decimal with 2 decimal places
                var formattedTotalAmount = totalAmount.toFixed(2);
                selectElement.closest('.form-row').find('.unit_price').val(unitPrice);
                selectElement.closest('.form-row').find('.total_amount').val(formattedTotalAmount);
            }

            // Bind the updateUnitPriceAndTotalAmount function to the change event of the stock dropdown/select element
            $('.stock_name').change(function() {
                updateUnitPriceAndTotalAmount($(this));
            });

            // Bind the updateUnitPriceAndTotalAmount function to the change event of the quantity input field
            $('.quantity').change(function() {
                updateUnitPriceAndTotalAmount($(this).closest('.form-row').find('.stock_name'));
            });

            // Function to update customer details
            function updateCustomerDetails(customer) {
                    $('#phone').val(customer.phone);
                    $('#email').val(customer.email);
                    $('#address').val(customer.address);
                }

                // Function to fetch customer details based on input text
                $('#name').on('input', function() {
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

       // Function to add and remove item rows
       function updateElementIndex(el, prefix, ndx) {
           var id_regex = new RegExp('(' + prefix + '-\\d+)');
           var replacement = prefix + '-' + ndx;
           if ($(el).attr("for")) $(el).attr("for", $(el).attr("for").replace(id_regex, replacement));
           if (el.id) el.id = el.id.replace(id_regex, replacement);
           if (el.name) el.name = el.name.replace(id_regex, replacement);
       }
       
       var total = 1;

       function cloneMore(selector, prefix) {
           var newElement = $(selector).clone(true);
           newElement.find(':input:not([type=button]):not([type=submit]):not([type=reset])').each(function() {
               var name = $(this).attr('name')
               if(name) {
                   name = name.replace('-' + (total-1) + '-', '-' + total + '-');
                   var id = 'id_' + name;
                   $(this).attr({'name': name, 'id': id}).val('').removeAttr('checked');
               }
           });
           newElement.find('.quantity').focus().val('1');
           newElement.find('label').each(function() {
               var forValue = $(this).attr('for');
               if (forValue) {
               forValue = forValue.replace('-' + (total-1) + '-', '-' + total + '-');
               $(this).attr({'for': forValue});
               }
           });
           total++;
           $('#id_' + prefix + '-TOTAL_FORMS').val(total);
           $(selector).after(newElement);
           return false;
       }
       
       function deleteForm(prefix, btn) {
           if (total > 1){
               btn.closest('.form-row').remove();
               var forms = $('.form-row');
               $('#id_' + prefix + '-TOTAL_FORMS').val(forms.length);
               for (var i=0, formCount=forms.length; i<formCount; i++) {
                   $(forms.get(i)).find(':input').each(function() {
                       updateElementIndex(this, prefix, i);
                   });
               }
               total--;
           } else {
               custom_alert.render('Field cannot be deleted');
           }
           return false;
       }

       $(document).on('click', '.add-form-row', function(e){
           e.preventDefault();
           cloneMore('.form-row:last', 'form');
           return false;
       });
       
       $(document).on('click', '.remove-form-row', function(e){
           e.preventDefault();
           deleteForm('form', $(this));
           return false;
       });

   </script>
@endsection
