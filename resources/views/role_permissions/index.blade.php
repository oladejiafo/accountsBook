@extends('layouts.app')

@section('title', 'Role Permissions')
@section('content')
<div class="row">
    <div class="col-md-6" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Role Permissions</div>
    <div class="col-md-6">
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

<table class="table table-css table-bordered table-hover">
    <thead class="thead-dark align-middle">
        <tr>
            <th width="30%">Role</th>
            <th width="70%">Permissions</th>
            {{-- <th>Actions</th> --}}
        </tr>
    </thead>
    <tbody class="align-middle">
        @foreach ($rolePermissions->groupBy('role_id') as $roleId => $permissions)
            <tr>
                <td>{{ optional($permissions->first()->role)->name }}</td>
                <td>
                    @foreach ($permissions as $permission)
                        <span class="badge badge-success rounded-pill py-2 px-3 mr-2 mb-3" style="background-color: #deb94a;">
                            {{ optional($permission->permission)->label }}
                            <a href="{{ route('role-permissions.edit', $permission->id) }}" class="badge-link"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('role-permissions.destroy', $permission->id) }}" method="POST" class="badge-link-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="badge-link-button btn-danger" onclick="return confirm('Are you sure you want to delete this role permission?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </span>
                    @endforeach
                </td>
                {{-- <td>
                    <a href="{{ route('role-permissions.edit', $permissions->first()->id) }}" class="btn btn-info"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('role-permissions.destroy', $permissions->first()->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete all permissions for this role?')"><i class="fas fa-trash-alt"></i> Delete</button>
                    </form>
                </td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
{{-- @if ($rolePermissions->isNotEmpty())
<div class="pagination">
    {{ $rolePermissions->links() }}
</div>
@endif --}}

@endsection
