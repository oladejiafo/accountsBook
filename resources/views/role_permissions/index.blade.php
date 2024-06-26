@extends('layouts.app')


@section('title', 'Role Permissions')
@section('content')
<div class="row">
    <div class="col-6" style="color: #4e4e4e; font-style: bold;">Role Permissions</div>
    <div class="col-6">
        <div style="float:right;" class="d-flex justify-content-end mt-3">
            <div>
                <a href="{{ route('role-permissions.create') }}" class="btn btn-success mb-3">Create Role Permission</a>
            </div>
        </div>
    </div>
</div>

<div style="border-bottom: 1px solid white;"></div>
<form method="GET" action="{{ route('role-permissions.index') }}">
    <div class="input-group search">
        <input type="text" name="search" class="form-control textinput" placeholder="Search for permissions">
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
            <th>Role</th>
            <th>Number of Permissions</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="align-middle">
        @foreach ($roles as $role)
            @if ($role->name !== 'Super_Admin')
            <tr>
                <td>{{ $role->name }}</td>
                <td>{{ $rolePermissionsCount[$role->id] }}</td>
                <td>
                    <a href="{{ route('role-permissions.view', $role->id) }}" class="btn btn-info" title="View Permissions">
                            <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
</div>

@endsection
