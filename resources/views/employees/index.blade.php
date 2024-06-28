@extends('layouts.app')
@section('title', 'Employee List')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row" style="color: #4e4e4e; font-style: bold;">
                    <div class="col-8 titles">Employee List</div>
                    <div class="col-4">
                        @can('create',\App\Models\Employee::class)
                        <div style="float:right;">
                            <a class="btn btn-success" href="{{ route('employees.create') }}">Add New Employee</a>
                        </div>
                        @endcan
                    </div>
                </div>

                <div style="border-bottom: 1px solid white;"></div>
                <form method="GET" action="{{ route('employees.index') }}">
                    <div class="input-group search">
                        <input type="text" name="search" class="form-control textinput"
                            placeholder="Search by employee name">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-pink"
                                style="border-radius:0 .5rem .5rem 0 !important">Search</button>
                        </div>
                    </div>
                </form>
                <br>

                <div class="table-responsive">
                <table class="table table-bordered table-hover ">
                    <thead class="thead-light align-middle">
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">Full Name</th>
                            <th class="align-middle">Email</th>
                            <th class="align-middle">Phone</th>
                            <th class="align-middle">Department</th>
                            <th class="align-middle">Designation</th>
                            <th class="align-middle">Branch</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td class="align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $employee->first_name }} {{ $employee->middle_name }}
                                    {{ $employee->sur_name }}</td>
                                <td class="align-middle">{{ $employee->email }}</td>
                                <td class="align-middle">{{ $employee->phone }}</td>
                                <td class="align-middle">{{ optional($employee->department)->name }}</td>

                                <td class="align-middle">{{ optional($employee->designation)->name }}</td>

                                <td class="align-middle"></td>

                                <td class="align-middle">{{ $employee->active_status ? 'Active' : 'Inactive' }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('employees.show', $employee->id) }}"
                                        class="btn btn-info btn-sm">View</a>
                                    @can('update',$employee)
                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                        class="btn btn-primary btn-sm">Edit</a>
                                    @endcan
                                    @can('delete',$employee)
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                @if ($employees->isNotEmpty())
                <div class="row mb-3">
                    <div class="col-md-3  d-flex align-items-center">
                        <form id="perPageForm" method="GET" action="{{ route('employees.index') }}" class="form-inline">
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
                            {{ $employees->appends(['per_page' => request('per_page')])->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
