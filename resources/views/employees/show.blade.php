<!-- resources/views/employees/show.blade.php -->
@extends('layouts.app')
@section('title', 'View Employee')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Employee Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" value="{{ $employee->first_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" class="form-control" value="{{ $employee->middle_name }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sur_name">Surname:</label>
                                <input type="text" class="form-control" value="{{ $employee->sur_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <input type="text" class="form-control" value="{{ $employee->gender }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Add more fields with the same pattern -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="designation">Designation:</label>
                                <input type="text" class="form-control" value="{{ $employee->designation->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_promotion">Last Promotion:</label>
                                <input type="text" class="form-control" value="{{ $employee->last_promotion }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Continue adding more fields -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bank">Bank:</label>
                                <input type="text" class="form-control" value="{{ $employee->bank->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_number">Account Number:</label>
                                <input type="text" class="form-control" value="{{ $employee->account_number }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Add remaining fields -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch">Branch:</label>
                                <input type="text" class="form-control" value="{{ $employee->branch->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state_of_origin">State of Origin:</label>
                                <input type="text" class="form-control" value="{{ $employee->state_of_origin->name }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="LGA_of_origin">LGA of Origin:</label>
                                <input type="text" class="form-control" value="{{ $employee->LGA_of_origin->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">Department:</label>
                                <input type="text" class="form-control" value="{{ $employee->department->name }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Add more fields as needed -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="text" class="form-control" value="{{ $employee->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" class="form-control" value="{{ $employee->phone }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Add edit and close buttons -->
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">Edit</a>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
