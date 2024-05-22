@extends('layouts.app')

@section('title', $title)

@section('content')

    <div class="titles" style="color:#575757; font-style: bold; border-bottom: 1px solid white;">{{ $title }}</div> 
    
    <br>
    
    <form method="post" action="{{ route('store-stock') }}">
    
        @csrf
        
        <div class="form-group">
            <label for="category">Category:</label>
            <select class="form-control" id="category" name="category">
                <option value="">Select Category</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
            <input type="hidden" name="company" id="company" class="form-control" value="{{ auth()->user()->company_id ?? 1 }}">

        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="price">Unit Price:</label>
            <input type="number" name="price" id="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="reorder">Reorder Point:</label>
            <input type="number" name="reorder" id="reorder" class="form-control">
        </div>

        <div class="form-group">
            <label for="location">Stock Location:</label>
            <select name="location" id="location" class="form-control">
                <option value="">Select Stock Location</option>
                @foreach($stockLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <br>

        <div class="align-middle">
            <button type="submit" class="btn btn-success">Add To Inventory</button>
            @if ($title == "New Stock")
                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
            @endif
            @if (isset($delbtn))
                <a href="{{ route('delete-stock', $object->pk) }}" class="btn btn-danger">Delete Stock</a>
            @endif
            <a href="{{ route('inventory') }}" class="btn btn-secondary">Cancel</a>
        </div>
        
    </form>

    <script>
        function resetForm() {
            document.getElementById("category").value = "";
            document.getElementById("name").value = "";
            document.getElementById("quantity").value = "";
            document.getElementById("location").value = "";
            document.getElementById("description").value = "";
        }
    </script>

@endsection
