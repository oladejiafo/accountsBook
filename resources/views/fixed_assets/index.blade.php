@extends('layouts.app')
@section('title', 'Assets List')
@section('content')

<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="titles" style="color: #4e4e4e; font-weight: bold;">Fixed Assets List</h2>
        </div>
        <div class="col-md-4 text-right">
            <a class="btn btn-success" href="{{ route('fixed_assets.create') }}">Add Fixed Asset</a>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        {{ $message }}
    </div>
    @endif

    <div style="border-bottom: 1px solid #ddd; margin-bottom: 20px;"></div>

    <form method="GET" action="{{ route('fixed_assets.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control textinput" placeholder="Search by asset name">
            <div class="input-group-append">
                <button type="submit" class="btn btn-pink" style="border-radius: 0 .5rem .5rem 0;">Search</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-hover table-responsive-md">
        <thead class="thead-dark">
            <tr>
                <th class="align-middle text-center">Name</th>
                <th class="align-middle text-center">Acquisition Date</th>
                <th class="align-middle text-center">Cost</th>
                <th class="align-middle text-center">Status</th>
                <th class="align-middle text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fixedAssets as $fixedAsset)
            <tr>
                <td class="align-middle">{{ $fixedAsset->name }}</td>
                <td class="align-middle">{{ $fixedAsset->acquisition_date }}</td>
                <td class="align-middle">{{ $fixedAsset->cost }}</td>
                <td class="align-middle">{{ $fixedAsset->status }}</td>
                <td class="align-middle">
                    <a href="{{ route('fixed_assets.show', $fixedAsset->id) }}" class="btn btn-info btn-sm">Show</a>
                    <a href="{{ route('fixed_assets.edit', $fixedAsset->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('fixed_assets.destroy', $fixedAsset->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center">
        {{ $fixedAssets->links() }}
    </div>
</div>
@endsection
