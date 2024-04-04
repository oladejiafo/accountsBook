@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">
                    <div class="col-md-8">Inventory List</div>
                    <div class="col-md-4">
                        <div style="float:right;">
                            <a class="btn btn-success" href="{{ route('new-stock') }}">Add New Stock</a>
                        </div>
                    </div>
                </div>

                <div style="border-bottom: 1px solid white;"></div>
                <form method="GET" action="{{ route('inventory') }}">
                    <div class="input-group search">
                        <input type="text" name="search" class="form-control textinput" placeholder="Search by stock name">
                        <div class="input-group-append">
                           <button type="submit" class="btn btn-pink">Search</button>
                        </div>
                    </div>
                </form>
                <br>

                <table class="table table-css table-bordered table-hover">
                    <thead class="thead-dark align-middle">
                        <tr>
                            <th class="align-middle">Stock Category</th>
                            <th class="align-middle" width="20%">Stock Name</th>
                            <th class="align-middle">Current Stock in Inventory</th>
                            <th class="align-middle">Location</th>
                            <th class="align-middle">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($stocks->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">The records are empty. Please try adding some.</td>
                            </tr>
                        @else
                            @foreach ($stocks as $stock)
                            {{-- @dd($stock->storeLocation->name) --}}
                                <tr>
                                    <td class="align-middle">{{ $stock->category ? $stock->category->name : 'N/A' }}</td>
                                    <td class="align-middle">{{ $stock->name }}</td>
                                    <td class="align-middle">{{ $stock->quantity }}</td>
                                    <td class="align-middle">{{ $stock->storeLocation->name }}</td>
                                    <td class="align-middle">
                                        <a href="{{ route('edit-stock', $stock->id) }}" class="btn btn-info btn-sm">Edit Details</a>
                                        <form action="{{ route('delete-stock', $stock->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete Stock</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="align-middle">
                    @if ($stocks->isNotEmpty())
                        <!-- Handle pagination here if necessary -->
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
