@extends('layouts.app')

@section('title', 'Payrolls')

@section('content')

<div class="container">
    @if (auth()->user()->hasPermission('payrolls.index') || auth()->user()->hasRole('Super_Admin'))
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="titles" style="color: #4e4e4e; font-weight: bold;">Payrolls List</h2>
        </div>
        <div class="col-md-4 text-right">
            <a class="btn btn-success" href="{{ route('payrolls.create') }}">Create New Payroll</a>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        {{ $message }}
    </div>
    @endif

    <div style="border-bottom: 1px solid #ddd; margin-bottom: 20px;"></div>

    <form method="GET" action="{{ route('payrolls.index') }}" class="mb-3">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search by employee name">
            <div class="input-group-append">
                <button type="submit" class="btn btn-pink" style="border-radius: 0 .5rem .5rem 0;">Search</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Employee</th>
                    <th>Basic Salary</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Total Pay</th>
                    <th>Payment Date</th>
                    <th>Period</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                    <td>{{ $payroll->basic_salary }}</td>
                    <td>{{ $payroll->allowances }}</td>
                    <td>{{ $payroll->deductions }}</td>
                    <td>{{ $payroll->total_pay }}</td>
                    <td>{{ $payroll->payment_date }}</td>
                    <td>{{ $payroll->period }}</td>
                    <td>
                        <a href="{{ route('payrolls.show', $payroll->id) }}" class="btn btn-sm btn-primary">View</a>
                        <form action="{{ route('payrolls.rollback', $payroll->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Rollback</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination links -->
    @if ($payrolls->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-3 d-flex align-items-center">
            <form id="perPageForm" method="GET" action="{{ route('payrolls.index') }}" class="form-inline">
                <label for="per_page" class="mr-2" style="font-size: 13px">Records per page:</label>
                <select name="per_page" id="per_page" class="form-control" style="width: 65px"
                    onchange="document.getElementById('perPageForm').submit();">
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="75" {{ request('per_page') == 75 ? 'selected' : '' }}>75</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <div class="pagination">
                {{ $payrolls->appends(['per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
    @endif
    @else
    <p>You do not have permission to view payrolls.</p>
    @endif
</div>

@endsection
