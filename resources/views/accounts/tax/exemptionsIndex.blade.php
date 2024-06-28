@extends('layouts.app')

@section('title', 'Tax Exemptions')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold;">Tax Exemptions</div>

                    <div class="card-body">
                        <p>This section displays a list of tax exemptions available for your company.</p>

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <a href="{{ route('tax-exemptions.create') }}" class="btn btn-success mb-3"
                            style="float: right">Create Tax Exemption</a>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Valid From</th>
                                        <th>Valid To</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($taxExemptions as $exemption)
                                        <tr>
                                            <td>{{ $exemption->id }}</td>
                                            <td>{{ $exemption->name }}</td>
                                            <td>{{ $exemption->description }}</td>
                                            <td>{{ $exemption->valid_from }}</td>
                                            <td>{{ $exemption->valid_to }}</td>
                                            <td>
                                                <a href="{{ route('tax-exemptions.edit', $exemption->id) }}"
                                                    class="btn btn-info">Edit</a>
                                                <form action="{{ route('tax-exemptions.destroy', $exemption->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this tax exemption?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No tax exemptions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($taxExemptions->isNotEmpty())
                            <div class="row mb-3">
                                <div class="col-md-3  d-flex align-items-center">
                                    <form id="perPageForm" method="GET" action="{{ route('tax-exemptions.index') }}" class="form-inline">
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
                                        {{ $taxExemptions->appends(['per_page' => request('per_page')])->links() }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
