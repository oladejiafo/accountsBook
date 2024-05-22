@extends('layouts.app')

@section('title', 'Edit Chart of Account')

@section('content')
<div class="container">
    <h1  class="titles" style="color: #4e4e4e; font-style: bold; ">Edit Chart of Account</h1>
    <form action="{{ route('chartOfAccounts.update', $chartOfAccount->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="category">Category:</label>
            <select class="form-control" id="category" name="category" required>
                <option value="" disabled>Select Category</option>
                <option value="Asset" {{ $chartOfAccount->category === 'Asset' ? 'selected' : '' }}>Asset</option>
                <option value="Liability" {{ $chartOfAccount->category === 'Liability' ? 'selected' : '' }}>Liability</option>
                <option value="Equity" {{ $chartOfAccount->category === 'Equity' ? 'selected' : '' }}>Equity</option>
                <option value="Income" {{ $chartOfAccount->category === 'Income' ? 'selected' : '' }}>Income</option>
                <option value="Expense" {{ $chartOfAccount->category === 'Expense' ? 'selected' : '' }}>Expense</option>
            </select>
        </div>
        <div class="form-group">
            <label for="type">Type:</label>
            <select class="form-control" id="type" name="type" required>
                <option value="" disabled selected>Select Type</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                @endforeach
            </select>            
        </div>
        {{-- <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $chartOfAccount->name }}" required>
        </div> --}}
        <div class="form-group">
            <label for="code">Code:</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $chartOfAccount->code }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $chartOfAccount->description }}</textarea>
        </div>


        <br>

        <div class="align-middle">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('chartOfAccounts.destroy', $chartOfAccount->id) }}" class="btn btn-danger">Delete</a>
            <a href="{{ route('chartOfAccounts') }}" class="btn btn-secondary">Cancel</a>
        </div>
        
    </form>

    <script>
        function resetForm() {
            document.getElementById("category").value = "";
            document.getElementById("name").value = "";
            document.getElementById("quantity").value = "";
            document.getElementById("type").value = "";
            document.getElementById("code").value = "";
            document.getElementById("description").value = "";
        }
    </script>
</div>
@endsection
