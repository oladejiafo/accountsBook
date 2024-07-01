@extends('layouts.app')

@section('title', 'Create Payroll')

@section('content')
<div class="container">
    @if (auth()->check() && auth()->user()->company_id)
        <div class="card">
            <div class="card-header" style="color: #4e4e4e; font-weight: bold;">
                Create Payroll
            </div>
            <div class="card-body">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            
                <form action="{{ route('payrolls.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="employee_id">Select Employee:</label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="year">Year:</label>
                            <input type="number" name="year" id="year" class="form-control" placeholder="Enter year" value="{{ date('Y') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="month">Month:</label>
                            <select name="month" id="month" class="form-control" required>
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="align-middle mt-5">
                        <button type="submit" class="btn btn-lg btn-success">Generate Payroll</button>
                        <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                        <a href="{{ route('payrolls.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    @else
        <p>You do not have permission to view this page.</p>
    @endif
</div>
@endsection
