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
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}">
        </div>
        <div class="align-middle">
                                <button type="submit" class="btn btn-lg btn-success">Update User</button>
                                <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                            </div>
    </form>
    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
