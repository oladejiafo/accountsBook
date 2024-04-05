@extends("layouts.app")

@section("content")

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Select Supplier') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('purchase.create') }}">
                        @csrf

                        <!-- Supplier Selection -->
                        <div class="form-group">
                            <label for="supplier">{{ __('Select Supplier') }}</label>
                            <select id="supplier" class="form-control" name="supplier" required>
                                <option value="">------</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Other Supplier Details -->
                        <!-- Add other supplier details fields here -->

                        <button type="submit" class="btn btn-success">{{ __('Next') }}</button>
                        <a href="{{ route('purchase.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
