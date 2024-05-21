@extends('layouts.app')

@section('title', 'View User')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" style="color: #4e4e4e; font-style: bold; ">View User</h1>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" value="{{ $user->name }}" readonly>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
            </div>

            <div class="form-group">
                <label for="department_id">Department:</label>
                <input type="text" class="form-control" id="department_id" name="department_id" value="{{ $user->department ? $user->department->name : '---' }}" readonly>
            </div>
        
            <div class="form-group">
                <label for="designation_id">Designation:</label>
                <input type="text" class="form-control" id="designation_id" name="designation_id" value="{{ $user->designation ? $user->designation->name : '---'  }}" readonly>
            </div>

            <div class="form-group">
                <label for="roles">Roles:</label><br>
                @foreach($user->roles as $role)
                    <span class="badge badge-custom" style="font-size: 1.3em; background-color: #ff6600; color: #ffffff;">{{ $role->name }}</span>
                @endforeach
            </div>

            <div class="align-middle">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info">Edit</a>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                </form>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
