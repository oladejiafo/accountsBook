@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                
                <div class="row card-header">
                    <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Customers</div>
                    <div class="col-md-6">
                        <div style="float:right;" class="d-flex justify-content-end mt-3">
                            <div>
                                <a href="{{ route('customers.create') }}" class="btn btn-success mb-3">Create Customer</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="border-bottom: 1px solid white;"></div>
                <form method="GET" action="{{ route('customers.index') }}">
                    <div class="input-group search">
                        <input type="text" name="search" class="form-control textinput" placeholder="Search for customers">
                        <div class="input-group-append">
                        <button type="submit" class="btn btn-pink">Search</button>
                        </div>
                    </div>
                </form>
                <br>

                <div class="card-body">
                    <table class="table table-css table-bordered table-hover">
                        <thead class="thead-dark align-middle">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td><a href="{{ route('customers.show', $customer->id) }}">{{ $customer->name }}</a></td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-info btn-sm">Edit</a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($customers->isNotEmpty())
        <div class="pagination">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
