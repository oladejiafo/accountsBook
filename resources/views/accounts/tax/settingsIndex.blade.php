@extends('layouts.app')
@section('title', 'Tax Settings')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 3rem;">Tax Settings</div>
                    <p class="text-muted">Configure various tax-related settings for your business.</p>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <a href="{{ route('tax-settings.create') }}" class="btn btn-success mb-3 ml-auto justify-content-end" style="float:right;">Create Tax Setting</a>

                        <table class="table table-css table-bordered table-hover">
                            <thead class="thead-dark align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>Setting Name</th>
                                    <th>Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($taxSettings as $taxSetting)
                                    <tr>
                                        <td>{{ $taxSetting->id }}</td>
                                        <td>{{ $taxSetting->name }}</td>
                                        <td>{{ $taxSetting->value }}</td>
                                        <td class="align-middle">
                                            <a href="{{ route('tax-settings.edit', $taxSetting->id) }}" class="btn btn-info">Edit</a>
                                            <form action="{{ route('tax-settings.destroy', $taxSetting->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this tax setting?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No tax settings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
