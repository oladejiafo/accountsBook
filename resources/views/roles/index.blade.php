@extends('layouts.app')

@section('title', 'Roles Management')
@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">User Roles</div>
        <div class="col-md-6">
            <div style="float:right;" class="d-flex justify-content-end mt-3">
                <div>
                    <a href="{{ route('roles.create') }}" class="btn btn-success mb-3">Create User roles</a>
                </div>
            </div>
        </div>
    </div>

    <div style="border-bottom: 1px solid white;"></div>
    <form method="GET" action="{{ route('roles.index') }}">
        <div class="input-group search">
            <input type="text" name="search" class="form-control textinput" placeholder="Search for roles">
            <div class="input-group-append">
               <button type="submit" class="btn btn-pink" style="border-radius:0 .5rem .5rem 0 !important">Search</button>
            </div>
        </div>
    </form>
    
    <br>

    <table class="table table-css table-bordered table-hover">
        <thead class="thead-dark align-middle">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Guard Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->guard_name }}</td>
                                        <td>
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info">Edit</a>
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
