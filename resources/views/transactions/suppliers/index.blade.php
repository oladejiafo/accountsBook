@extends('layouts.app')

@section('title', 'Suppliers List')

@section('content')
<div class="row" style="color: #575757; font-style: bold; font-size: 3rem;">
    <div class="col-md-8">Suppliers List</div>
    <div class="col-md-4">
        <div style="float:right;"> <a class="btn btn-success" href="{{ route('supplier.create') }}">Add New Supplier</a> </div>
    </div>
</div>

<br>

<table class="table table-css table-hover table-bordered">
    <thead class="thead-dark align-middle">
        <tr>
            <th width="20%">Name</th>
            <th width="15%">Phone</th>
            <th width="15%">Email</th>
            <th width="20%">GSTIN No</th>
            <th width="30%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($suppliers as $supplier)
        <tr>
            <td>
                <p><a href="{{ route('supplier', $supplier->id) }}">{{ $supplier->name }}</a></p>
            </td>
            <td class="align-middle">{{ $supplier->phone }}</td>
            <td class="align-middle">{{ $supplier->email }}</td>
            <td class="align-middle">{{ $supplier->gstin }}</td>
            <td class="align-middle">
                <div class="align-middle">
                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-info btn-sm">Edit Details</a>
                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete Supplier</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No suppliers found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="align-middle">
    {{ $suppliers->links() }}
</div>
@endsection
