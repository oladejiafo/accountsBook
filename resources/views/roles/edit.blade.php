@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Edit Role</div>

                    <div class="card-body">
                        <form action="{{ route('roles.update', ['id' => $role->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}">
                            </div>
                            <div class="form-group">
                                <label for="guard_name">Guard Name</label>
                                <input type="text" name="guard_name" id="guard_name" class="form-control" value="{{ $role->guard_name }}">
                            </div>
                            <br>

                            <div class="align-middle">
                                <button type="submit" class="btn btn-lg btn-success">Update User</button>
                                <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-lg btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
