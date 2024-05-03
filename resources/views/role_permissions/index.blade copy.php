@extends('layouts.app')

    @section('title', 'Role Permissions')
    @section('content')
            <div class="row">
                <div class="col-md-6"  style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Role Permissions</div>
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
                    <th>Role</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rolePermissions as $rolePermission)
                    <tr>
                        <td>{{ optional($rolePermission->role)->name }}</td>
                        <td>{{ optional($rolePermission->permission)->label }}</td>

                        <td>
                            <a href="{{ route('role-permissions.edit', $rolePermission->id) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('role-permissions.destroy', $rolePermission) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role permission?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($rolePermissions->isNotEmpty())
            <div class="pagination">
                {{ $rolePermissions->links() }}
            </div>
        @endif
    </div>
@endsection
