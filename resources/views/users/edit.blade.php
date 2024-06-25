@extends('layouts.app')

@section('title', 'Edit User')

<style>
    /* Custom styles for checkboxes */
    input[type='checkbox'] {
        -webkit-appearance: none;
        width: 25px;
        height: 25px;
        background: white;
        border-radius: 5px;
        border: 2px solid #555;
    }

    input[type='checkbox']:checked {
        content: '\2713';
        font-size: 20px;
        color: #abd;
        position: absolute;
    }

    input[type='text'] {
        background-color: var(--white);
        padding-left: .75rem;
        padding-right: .75rem;
        color: var(--80);
    }

    input[type='text'] {
        border-width: 1px;
        border-color: var(--60);
        border-radius: .5rem;
    }
</style>
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title" style="color: #4e4e4e; font-style: bold;">Edit User</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="department_id">Department:</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="" disabled>Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="designation_id">Designation:</label>
                        <select class="form-control" id="designation_id" name="designation_id">
                            <option value="" disabled>Select Job Title</option>
                            @foreach ($designations as $designation)
                                <option value="{{ $jobTitle->id }}"
                                    {{ $user->designation_id == $jobTitle->id ? 'selected' : '' }}>
                                    {{ $designation->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="roles">Roles:</label><br>
                        @php $counter = 0 @endphp
                        @foreach ($roles as $role)
                            @if ($counter % 2 == 0)
                                <div class="row mb-2">
                            @endif
                            <div class="col-md-6 col-lg-6 col-sm-12 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}"
                                        name="roles[]" value="{{ $role->id }}"
                                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                    <label class="form-check-label ml-3" style="margin-top: -4px;"
                                        for="role_{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                            </div>
                            @if ($counter % 2 != 0 || $loop->last)
                    </div>
                    @endif
                    @php $counter++ @endphp
                    @endforeach

                    <br>
                    <div class="align-middle">
                        <button type="submit" class="btn btn-lg btn-success">Update User</button>
                        <button type="reset" class="btn btn-lg btn-danger" onclick="resetForm()">Reset</button>
                        <a href="{{ route('users.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function resetForm() {
                document.getElementById("name").value = "";
                document.getElementById("email").value = "";
                document.getElementById("password").value = "";
        
                document.getElementById("department_id").value = "";
                document.getElementById("designation_id").value = "";
                document.getElementById("role_{{ $role->id }}").value = "";
                
            }
        </script>
    @endsection
