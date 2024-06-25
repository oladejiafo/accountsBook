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
                <h1 class="card-title" style="color: #4e4e4e; font-style: bold; ">Create Role Permission</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('role-permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" id="role_id" class="form-control" required>
                            <option selected disabled>Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="permission_id">Permission</label>
                        <select name="permission_id[]" id="permission_id" class="form-control" multiple style="height: 200px;">
                            <option selected disabled>Select All Permissions Applicable</option>
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->label  }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="align-middle">
                        <button type="submit" class="btn  btn-lg btn-success">Create Role Permission</button>
                        <button type="reset" class="btn btn-lg btn-danger" onclick="resetForm()">Reset</button>
                        <a href="{{ route('role-permissions.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Function to reset form fields
        function resetForm() {
            document.getElementById("role_id").value = "";
            document.getElementById("permission_id").value = "";
        }
    </script>
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
