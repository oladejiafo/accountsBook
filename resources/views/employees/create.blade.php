@extends('layouts.app')
@section('title', 'Create Employee')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create New Employee</div>
                <div class="card-body">
                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="active_status">Active Status:</label>
                                <select name="active_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="staff_number">Staff Number:</label>
                                <input type="text" name="staff_number" class="form-control">
                            </div>
                        </div>
                        
                        <!-- More fields grouped into rows with two columns each -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="sur_name">Surname:</label>
                                <input type="text" name="sur_name" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name" class="form-control">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" class="form-control">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="designation_id">Designation:</label>
                                <select name="designation_id" class="form-control">
                                    @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="last_promotion">Last Promotion:</label>
                                <input type="date" name="last_promotion" class="form-control">
                            </div>
                        </div>

                        <!-- Continue grouping fields in two columns per row -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="level">Level:</label>
                                <input type="text" name="level" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="step">Step:</label>
                                <input type="text" name="step" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="cadre">Cadre:</label>
                                <input type="text" name="cadre" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="date_of_birth">Date of Birth:</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="height">Height:</label>
                                <input type="text" name="height" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="weight">Weight:</label>
                                <input type="text" name="weight" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="date_employed">Date Employed:</label>
                                <input type="date" name="date_employed" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="exit_date">Exit Date:</label>
                                <input type="date" name="exit_date" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="exit_reason">Exit Reason:</label>
                                <input type="text" name="exit_reason" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="bank_id">Bank:</label>
                                <select name="bank_id" class="form-control">
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Continue this pattern for the remaining fields -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="account_number">Account Number:</label>
                                <input type="text" name="account_number" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="blood_group">Blood Group:</label>
                                <input type="text" name="blood_group" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="genotype">Genotype:</label>
                                <input type="text" name="genotype" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="in_staff_qtrs">In Staff Quarters:</label>
                                <select name="in_staff_qtrs" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="region">Region:</label>
                                <input type="text" name="region" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="branch_id">Branch:</label>
                                <select name="branch_id" class="form-control">
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="state_of_origin_id">State of Origin:</label>
                                <select name="state_of_origin_id" class="form-control">
                                    <option selected disabled>Select</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="LGA_of_origin_id">LGA of Origin:</label>
                                <select name="LGA_of_origin_id" class="form-control">
                                    @foreach($LGAs as $lga)
                                    <option value="{{ $lga->id }}">{{ $lga->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="department_id">Department:</label>
                                <select name="department_id" class="form-control">
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="home_address">Home Address:</label>
                                <input type="text" name="home_address" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="contact_address">Contact Address:</label>
                                <input type="text" name="contact_address" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="personal_phone">Personal Phone:</label>
                                <input type="text" name="personal_phone" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="personal_email">Personal Email:</label>
                                <input type="email" name="personal_email" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="office_location">Office Location:</label>
                                <input type="text" name="office_location" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="employment_type">Employment Type:</label>
                                <input type="text" name="employment_type" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="marital_status">Marital Status:</label>
                                <select name="marital_status" class="form-control">
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="birth_place">Birth Place:</label>
                                <input type="text" name="birth_place" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="spouse_name">Spouse Name:</label>
                                <input type="text" name="spouse_name" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="marriage_date">Marriage Date:</label>
                                <input type="date" name="marriage_date" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="nationality_id">Nationality:</label>
                                <select name="nationality_id" class="form-control">
                                    @foreach($nationalities as $nationality)
                                    <option value="{{ $nationality->id }}">{{ $nationality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="qualifications_id">Qualifications:</label>
                                <select name="qualifications_id" class="form-control">
                                    
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="profession_id">Profession:</label>
                                <select name="profession_id" class="form-control">
                                    @foreach($professions as $profession)
                                    <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="confirmation_status">Confirmation Status:</label>
                                <input type="text" name="confirmation_status" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="position_id">Position:</label>
                                <select name="position_id" class="form-control">
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="date_confirmed">Date Confirmed:</label>
                                <input type="date" name="date_confirmed" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="pension_managers_id">Pension Manager:</label>
                                <select name="pension_managers_id" class="form-control">
                                    @foreach($pension_managers as $pension_manager)
                                    <option value="{{ $pension_manager->id }}">{{ $pension_manager->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="pension_amount">Pension Amount:</label>
                                <input type="text" name="pension_amount" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="deformity">Deformity:</label>
                                <input type="text" name="deformity" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="salary">Salary:</label>
                                <input type="text" name="salary" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="days_worked">Days Worked:</label>
                                <input type="text" name="days_worked" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="tax_id">Tax ID:</label>
                                <input type="text" name="tax_id" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="pension_pin">Pension PIN:</label>
                                <input type="text" name="pension_pin" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="passport_number">Passport Number:</label>
                                <input type="text" name="passport_number" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="residency_status">Residency Status:</label>
                                <input type="text" name="residency_status" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="visa_type">Visa Type:</label>
                                <input type="text" name="visa_type" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="visa_expiry">Visa Expiry:</label>
                                <input type="date" name="visa_expiry" class="form-control">
                            </div>
                        </div>

                        <div class="align-middle">
                            <button type="submit" class="btn btn-lg btn-success">Create User</button>
                            <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                            <a href="{{ route('employees.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
