@extends('layouts.app')
@section('title', 'Assets List')
@section('content')

<div class="container">
@if (auth()->user()->hasPermission('fixed_assets.index') || auth()->user()->hasRole('Super_Admin'))
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

    <div class="table-responsive">
    <table class="table table-bordered table-hover">
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
                <td class="align-middle">{{ $fixedAsset->purchase_price }}</td>
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
    </div>

            <!-- Pagination links -->
        @if ($fixedAssets->isNotEmpty())
        <div class="row mb-3">
            <div class="col-md-3  d-flex align-items-center">
                <form id="perPageForm" method="GET" action="{{ route('fixed_assets.index') }}" class="form-inline">
                    <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                    <select name="per_page" id="per_page" class="form-control" style="width: 65px" onchange="document.getElementById('perPageForm').submit();">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="75" {{ request('per_page') == 75 ? 'selected' : '' }}>75</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            <div class="col-md-9 d-flex justify-content-end">
                <div class="pagination">
                    {{ $fixedAssets->appends(['per_page' => request('per_page')])->links() }}
                </div>
            </div>
        </div>
        @endif
    @else
        <p>You do not have permission to fixed assets.</p>
    @endif
</div>
@endsection
