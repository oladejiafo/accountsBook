@extends('layouts.app')

@section('title', 'Users Management')
@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-6 titles"  style="color: #4e4e4e; font-style: bold;">Users</div>
        <div class="col-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-success mb-3">Create User</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('users.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for users">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    
    <br>

    <div class="table-responsive">
    <table class="table table-css table-bordered table-hover">
        <thead class="thead-dark align-middle">
            <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="align-middle">
            @foreach($users as $user)
                <tr>
                    <!-- <td>{{ $user->id }}</td> -->
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->department ? $user->department->name : '' }}</td>
                    <td>{{ $user->job_title ? $user->job_title->name : '' }}</td>
                    
                    <td>
                        @foreach($user->roles()->where('model_id', $user->id)->get() as $role)
                        <span class="badge badge-custom" style="font-size: 1.3em; background-color: #aec48b; color: #ffffff;">{{ $role->name }}</span>
                    @endforeach
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn btn-secondary" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn btn-info" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @if ($users->isNotEmpty())
        <div class="pagination">
            {{ $users->links() }}
        </div>
    @endif
@endsection
