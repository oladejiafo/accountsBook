<!-- resources/views/employees/show.blade.php -->
@extends('layouts.app')
@section('title','View Employee')
@section('content')
    <h1>Employee Details</h1>
    <table class="table mt-3">
        <tbody>
            <tr>
                <th>Full Name:</th>
                <td>{{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->sur_name }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $employee->email }}</td>
            </tr>
            <!-- Add other fields as needed -->
        </tbody>
    </table>
    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">Edit</a>
    <!-- Add delete button if necessary -->
@endsection
