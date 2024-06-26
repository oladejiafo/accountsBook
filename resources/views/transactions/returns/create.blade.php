@extends('layouts.app')

@section('title', 'New Return')

@section('content')

<div class="titles" style="color:#575757; font-weight: bold; border-bottom: 1px solid white;">New Return</div>

<br>

<form method="post" class="panel panel-default" action="{{ route('returns.process') }}">
    @csrf

    <div class="panel-heading panel-heading-text">Customer Details</div>
    <div class="panel-body">
        <div class="form-row">
        <div class="form-group col-md-6">
            <label for="customer_id" class="panel-body-text">Customer:</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-6">
            <label for="return_date" class="panel-body-text">Return date:</label>
            <input type="date" name="return_date" id="return_date" class="form-control" required>
            @error('return_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        </div>
    </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading panel-heading-text">Returned Items</div>
        <div class="panel-body">
            <div id="returnItems">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="product_id" class="panel-body-text">Product:</label>
                        <select name="items[0][product_id]" id="product_select" class="form-control" required>
                            <option value="">Select Product</option>
                            {{-- <option value="3">Mafia mgr</option> --}}
                        </select>
                        @error('items.0.product_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label for="quantity" class="panel-body-text">Quantity:</label>
                        <input type="number" name="items[0][quantity]" class="form-control" min="1" required>
                        @error('items.0.quantity')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label for="price" class="panel-body-text">Total Price:</label>
                        <input type="number" name="items[0][price]" class="form-control" min="1" required>
                        @error('items.0.price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="condition" class="panel-body-text">Condition:</label>
                        <input type="text" name="items[0][condition]" class="form-control" required>
                        @error('items.0.condition')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-12">
                        <label for="reason" class="panel-body-text">Reason:</label>
                        <textarea name="items[0][reason]" class="form-control" rows="2" required></textarea>
                        @error('items.0.reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-right">
                    <a href="#" class="add-return-item">+ Add Item</a>
                </div>
            </div>
        </div>
    </div>

    <div class="align-middle">
        <button type="submit" class="btn btn-success">Process Return</button>
        <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>

<!-- Custom JS to add and remove item rows -->
<script type="text/javascript" src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var returnItemTemplate = `
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="product_id" class="panel-body-text">Product:</label>
                <select name="items[__index__][product_id]" class="form-control" required>
                    <option value="">Select Product</option>
                    <!-- Populate with products dynamically -->
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="quantity" class="panel-body-text">Quantity:</label>
                <input type="number" name="items[__index__][quantity]" class="form-control" min="1" required>
            </div>
            <div class="form-group col-md-2">
                <label for="price" class="panel-body-text">Total Price:</label>
                <input type="number" name="items[0][price]" class="form-control" min="1" required>
                @error('items.0.price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="condition" class="panel-body-text">Condition:</label>
                <input type="text" name="items[__index__][condition]" class="form-control" required>
            </div>
            <div class="form-group col-md-12">
                <label for="reason" class="panel-body-text">Reason:</label>
                <textarea name="items[__index__][reason]" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-group col-md-1">
                <button type="button" class="btn btn-danger remove-return-item">-</button>
            </div>
        </div>`;

        var index = 0;

        // Add return item
        $('.add-return-item').click(function(e) {
            e.preventDefault();
            var template = returnItemTemplate.replace(/__index__/g, index++);
            $('#returnItems').append(template);
        });

        // Remove return item
        $(document).on('click', '.remove-return-item', function(e) {
            e.preventDefault();
            $(this).closest('.form-row').remove();
        });


        const customerSelect = document.getElementById('customer_id');
        const productSelect = document.getElementById('product_select');
        const quantityInput = document.querySelector('input[name="items[0][quantity]"]');
        const priceInput = document.querySelector('input[name="items[0][price]"]');

        customerSelect.addEventListener('change', function() {
            const customerId = customerSelect.value;

            if (customerId) {
                fetch(`/get-products/${customerId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Clear previous options
                        productSelect.innerHTML = '<option value="">Select Product</option>';

                        // Populate products
                        data.products.forEach(product => {
                            const option = document.createElement('option');
                            option.value = product.id;
                            option.textContent = product.name;
                            option.dataset.quantity = product.quantity; 
                            option.dataset.totalprice = product.totalprice; 
                            productSelect.appendChild(option);
                        });

                        // Update quantity input if a product is already selected
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        if (selectedOption) {
                            quantityInput.value = selectedOption.dataset.quantity || 1;
                            priceInput.value = selectedOption.dataset.totalprice || '--';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                    });
            } else {
                productSelect.innerHTML = '<option value="">Select Product</option>';
            }
        });

        productSelect.addEventListener('change', function() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const quantity = selectedOption.dataset.quantity || 1; 
            const totalprice = selectedOption.dataset.totalprice || "--"; 
            quantityInput.value = quantity;
            priceInput.value = totalprice;
        });
        

    });
</script>

@endsection
