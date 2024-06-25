@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; ">Create Role</div>

                    <div class="card-body">
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" name="description" id="description" value="web" class="form-control">
                            </div>
                            <div class="align-middle">
                                <button type="submit" class="btn  btn-lg btn-success">Create User</button>
                                <button type="reset" class="btn btn-lg btn-danger" onclick="resetForm()">Reset</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Function to reset form fields
        function resetForm() {
            document.getElementById("name").value = "";
            document.getElementById("description").value = "";
        }
    </script>
@endsection
