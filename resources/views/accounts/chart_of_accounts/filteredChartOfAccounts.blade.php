@extends('layouts.app')

@section('title', 'Filtered Chart of Accounts')

@section('content')
<div class="container">
    <h1>Filtered Chart of Accounts (Type: {{ $type }})</h1>
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartOfAccounts as $account)
            <tr>
                <td>{{ $account->code }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->description }}</td>
                <td>{{ $account->category }}</td>
                <td>
                    <a href="{{ route('chartOfAccounts.edit', $account->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('chartOfAccounts.destroy', $account->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this account?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
