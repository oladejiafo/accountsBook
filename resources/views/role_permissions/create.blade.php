@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="card">
            <div class="card-header">
                <h1 class="card-title" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Create Role Permission</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('role-permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" id="role_id" class="form-control">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="permission_id">Permission</label>
                        <select name="permission_id" id="permission_id" class="form-control">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="align-middle">
                        <button type="submit" class="btn  btn-lg btn-success">Create User</button>
                        <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                        <a href="{{ route('role-permissions.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
