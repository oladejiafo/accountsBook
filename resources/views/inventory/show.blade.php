@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')

    <div class="titles" style="color:#575757; font-weight: bold; border-bottom: 1px solid white;">Stock Details</div> 
    
    <br>
    
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" value="{{ $stock->category->name }}" readonly>
            </div>
            
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" value="{{ $stock->name }}" readonly>
            </div>
            
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" value="{{ $stock->quantity }}" readonly>
            </div>

            <div class="form-group">
                <label for="price">Unit Price:</label>
                <input type="number" class="form-control" value="{{ $stock->price }}" readonly>
            </div>

            <div class="form-group">
                <label for="reorder">Reorder Point:</label>
                <input type="number" class="form-control" value="{{ $stock->reorder_point }}" readonly>
            </div>

            {{-- <div class="form-group">
                <label for="location">Stock Location:</label>
                <input type="text" class="form-control" value="{{ $stock->location->name }}" readonly>
            </div> --}}
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" readonly>{{ $stock->description }}</textarea>
            </div>

            <div class="align-middle">
                <a href="{{ route('edit-stock', $stock->id) }}" class="btn btn-primary">Edit Stock</a>
                <a href="{{ route('delete-stock', $stock->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this stock?')">Delete Stock</a>
                <a href="{{ route('inventory') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

@endsection
