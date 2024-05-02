@extends('layouts.app')

@section('title', 'Create User')

<style>
    /* Custom styles for checkboxes */
    input[type='checkbox'] {
        -webkit-appearance:none;
        width:25px;
        height:25px;
        background:white;
        border-radius:5px;
        border:2px solid #555;
    }
    input[type='checkbox']:checked {
        content: '\2713';
        font-size: 20px;
        color: #abd; 
        position: absolute; 
    }
    </style>
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create User</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="department_id">Department:</label>
                    <select class="form-control" id="department_id" name="department_id">
                        <option value="" selected disabled>Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="designation_id">Job Title:</label>
                    <select class="form-control" id="designation_id" name="designation_id">
                        <option value="" selected disabled>Select Designation</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="roles">Roles:</label><br>
                    @foreach($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}">
                            <label class="form-check-label ml-3" style="margin-top: -5px;" for="role_{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                    @endforeach
                </div>

                <br>

                <div class="align-middle">
                    <button type="submit" class="btn  btn-lg btn-success">Create User</button>
                    <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                    <a href="{{ route('users.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
