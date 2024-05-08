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
    <tbody class="align-middle">
        @foreach ($rolePermissions as $rolePermission)
        <tr>
            <td>
                @if ($editing)
                    <!-- Show dropdown for editing -->
                    <select name="role_id" class="form-control">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $role->id == $rolePermission->role_id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                @else
                    <!-- Show role name -->
                    {{ optional($rolePermission->role)->name }}
                @endif
            </td>
            <td>
                @if ($editing)
                    <!-- Show dropdown for editing -->
                    <select name="permission_id" class="form-control">
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->id }}" {{ $permission->id == $rolePermission->permission_id ? 'selected' : '' }}>{{ $permission->label }}</option>
                        @endforeach
                    </select>
                @else
                    <!-- Show permission label -->
                    {{ optional($rolePermission->permission)->label }}
                @endif
            </td>
    
            <td>
                @if ($editing)
                    <!-- Show save button -->
                    <button class="btn btn-success">Save</button>
                @else
                  
                    <a href="{{ route('role-permissions.edit', $rolePermission->id) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    
                @endif
    
                <!-- Delete button always shown -->
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

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hide input fields initially
            $('.form-control').hide();

            // Toggle editing mode
            $('.toggle-editing').click(function() {
                $('.form-control').toggle();
                $('.toggle-editing').toggle();
            });

            // Cancel editing
            $('.cancel-editing').click(function() {
                $('.form-control').hide();
                $('.toggle-editing').show();
            });
        });
    </script>
@endsection
