@extends('layouts.app')

@section('title', 'Ledger')

@section('content')
<div class="container">
    <h1 style="color:black">Ledger</h1>
    {{-- <div class="mb-3">
        <a href="{{ route('ledger.create') }}" class="btn btn-success">Create New Ledger</a>
    </div> --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Account</th>
                    <th>Balance</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledgerEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->company->name }}</td>
                    <td>{{ $entry->account->name }}</td>
                    <td>{{ $entry->balance }}</td>
                    <td>{{ $entry->created_at }}</td>
                    <td>
                        <a href="{{ route('ledger.edit', $entry->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('ledger.destroy', $entry->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ledger entry?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
