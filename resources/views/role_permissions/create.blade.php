@extends('layouts.app')

@section('content')
<style>
    /* Custom styles for checkboxes */
    input[type='checkbox'] {
        -webkit-appearance:none;
        width:25px;
        height:25px;
        background:white;
        border-radius:5px;
        border:2px solid #555;
    }
    input[type='checkbox']:checked {
        content: '\2713';
        font-size: 20px;
        color: #abd; 
        position: absolute; 
    }
    </style>
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
                            <option selected disabled>Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="module_id">Module</label>
                        <select name="module_id" id="module_id" class="form-control">
                            <option selected disabled>Select Module</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sub_module_id">Sub Module</label>
                        <select name="sub_module_id" id="sub_module_id" class="form-control">
                            <option selected disabled>Select Sub-Modules</option>
                            @foreach ($subModules as $subModule)
                                <option value="{{ $subModule->id }}">{{ $subModule->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="permission_id">Permission</label>
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}" name="permission_id[]" value="{{ $permission->id }}">
                                        <label class="form-check-label ml-3" style="margin-top: -5px;" for="permission_{{ $permission->id }}">{{ $permission->label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="align-middle">
                        <button type="submit" class="btn  btn-lg btn-success">Create Role Permission</button>
                        <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                        <a href="{{ route('role-permissions.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- <script>
        $(document).ready(function () {
            $('#module_id').on('change', function () {
                var moduleId = $(this).val();
                $.ajax({
                    url: '/get-sub-modules/' + moduleId,
                    type: 'GET',
                    success: function (data) {
                        $('#sub_module_id').empty();
                        $.each(data, function (key, value) {
                            $('#sub_module_id').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script> --}}
@endsection
