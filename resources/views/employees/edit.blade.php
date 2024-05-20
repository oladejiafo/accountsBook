<!-- resources/views/employees/edit.blade.php -->
@extends('layouts.app')
@section('title','Edit Employee')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Employee</div>
                <div class="card-body">
                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="active_status">Active Status:</label>
                                <select name="active_status" class="form-control">
                                    <option value="1" {{ $employee->active_status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $employee->active_status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="staff_number">Staff Number:</label>
                                <input type="text" name="staff_number" class="form-control" value="{{ $employee->staff_number }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="sur_name">Surname:</label>
                                <input type="text" name="sur_name" class="form-control" value="{{ $employee->sur_name }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ $employee->middle_name }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" class="form-control">
                                    <option value="male" {{ $employee->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $employee->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $employee->gender == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="designation_id">Designation:</label>
                                <select name="designation_id" class="form-control">
                                    @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ $employee->designation_id == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="last_promotion">Last Promotion:</label>
                                <input type="date" name="last_promotion" class="form-control" value="{{ $employee->last_promotion }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="level">Level:</label>
                                <input type="text" name="level" class="form-control" value="{{ $employee->level }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="step">Step:</label>
                                <input type="text" name="step" class="form-control" value="{{ $employee->step }}">
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="cadre">Cadre:</label>
                                <input type="text" name="cadre" class="form-control" value="{{ $employee->cadre }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="date_of_birth">Date of Birth:</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ $employee->date_of_birth }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="height">Height:</label>
                                <input type="text" name="height" class="form-control" value="{{ $employee->height }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="weight">Weight:</label>
                                <input type="text" name="weight" class="form-control" value="{{ $employee->weight }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" value="{{ $employee->email }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone"  class="form-control" value="{{ $employee->phone }}">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="date_employed">Date Employed:</label>
                            <input type="date" name="date_employed" class="form-control" value="{{ $employee->date_employed }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="exit_date">Exit Date:</label>
                            <input type="date" name="exit_date" class="form-control" value="{{ $employee->exit_date }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="exit_reason">Exit Reason:</label>
                            <input type="text" name="exit_reason" class="form-control" value="{{ $employee->exit_reason }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="bank_id">Bank:</label>
                            <select name="bank_id" class="form-control">
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ $employee->bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Continue adding fields -->

                    <div class="align-middle">
                        <button type="submit" class="btn btn-lg btn-success">Update Employee</button>
                        <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
