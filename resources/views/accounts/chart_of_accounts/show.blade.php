@extends('layouts.app')

@section('title', 'Chart of Account Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-weight: bold;">Chart of Account Details</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <p>{{ $chartOfAccount->category }}</p>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <p>{{ $chartOfAccount->type }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Code:</label>
                        <p>{{ $chartOfAccount->code }}</p>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <p>{{ $chartOfAccount->description }}</p>
                    </div>
                </div>
            </div>

            <div class="align-middle">
                <a href="{{ route('chartOfAccounts.edit', $chartOfAccount->id) }}" class="btn btn-info">Edit Chart of Account</a>
                <form action="{{ route('chartOfAccounts.destroy', $chartOfAccount->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Chart of Account</button>
                </form>
                <a href="{{ route('chartOfAccounts') }}" class="btn btn-secondary">Back to Chart of Accounts</a>
            </div>
        </div>
    </div>
</div>
@endsection
