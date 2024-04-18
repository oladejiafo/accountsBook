@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Transaction Account Mapping</div>

                    <div class="card-body">
                        <form method="POST" action="{{ isset($mapped) ? route('transaction-account-mapping.update', ['id' => $mapped->id]):route('transaction-account-mapping.store') }}">
                            @csrf
                            @if(isset($mapped))
                                @method('PUT')
                            @endif
                            <div class="form-group row">
                                <label for="transaction_type" class="col-md-4 col-form-label text-md-right">Transaction Type</label>
                            
                                <div class="col-md-6">
                                    <select id="transaction_type" class="form-control @error('transaction_type') is-invalid @enderror" name="transaction_type" required autocomplete="transaction_type">
                                        @if(!isset($mapped))
                                            <option value="" selected disabled>-- Select Transaction Type --</option>
                                        @endif
                                        @foreach($transactionTypes as $type)
                                            <option value="{{ $type->id }}" {{ isset($mapped) && $mapped->transaction_type == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('transaction_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>                            

                            <div class="form-group row">
                                <label for="account_id" class="col-md-4 col-form-label text-md-right">Account</label>

                                <div class="col-md-6">
                                    <select id="account_id" class="form-control @error('account_id') is-invalid @enderror" name="account_id" required autocomplete="account_id">
                                        @if(!isset($mapped))
                                            <option value="" selected disabled>-- Select Account --</option>
                                        @endif
                                        @foreach($chartOfAccounts as $account)
                                            <option value="{{ $account->id }}" {{ isset($mapped) && $mapped->account_id == $account->id ? 'selected' : '' }}>{{ $account->description }}</option>
                                        @endforeach
                                    </select>  
                                    @error('account_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="is_credit" class="col-md-4 col-form-label text-md-right">Credit?</label>
                                <div class="col-md-6">
                                    <input id="is_credit" type="checkbox" class="form-control @error('is_credit') is-invalid @enderror" name="is_credit" value="1" autocomplete="is_credit" {{ isset($mapped) ? ($mapped->is_credit ? 'checked' : '') : '' }}>
                                    @error('is_credit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>                            

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 justify-content-end">
                                    <button type="submit" class="btn btn-md btn-success">
                                        {{ isset($mapped) ? 'Update' : 'Submit' }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Existing Mappings Listing -->
                        @if($mappings->isNotEmpty())
                            <h2>Existing Transaction Account Mappings</h2>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction Type</th>
                                        <th>Account</th>
                                        <th>Credit?</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mappings as $mapping)
                                        <tr>
                                            <td>{{ $mapping->transaction_type }}</td>
                                            <td>{{ $mapping->account->description }}</td>
                                            <td>{{ $mapping->is_credit ? 'Yes' : 'No' }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <a href="{{ route('transaction-account-mapping.edit', $mapping->id) }}" class="btn btn-info">Edit</a>
                                                
                                                <!-- Delete Button (You can use a form submission for delete if needed) -->
                                                <form method="POST" action="{{ route('transaction-account-mapping.destroy', $mapping->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
