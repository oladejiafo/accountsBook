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
                    <thead class="thead-dark align-middle">
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">Full Name</th>
                            <th class="align-middle">Email</th>
                            <th class="align-middle">Phone</th>
                            <th class="align-middle">Department</th>
                            <th class="align-middle">Designation</th>
                            <th class="align-middle">Branch</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Actions</th>
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
                    <div class="pagination">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
