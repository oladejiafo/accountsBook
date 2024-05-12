@extends('layouts.app')
@section('title', 'Customer Details')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-4">
                    <div class="card-header" style="color: #4e4e4e; font-style: bold; font-size: 2rem;">Customer Details</div>
                    <div class="content-section">
                        <div class="media">
                            <div class="media-body">
                                <h2 style="color:#575757;" class="account-heading">&nbsp;{{ $customer->name }}</h2>
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-info" style="float: right;">Edit Details</a>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fal">
                                            <b>Contact &nbsp; &nbsp;&nbsp; :</b> {{ $customer->phone }} <br>
                                            <b>Email Id &nbsp;&nbsp;&nbsp;&nbsp; :</b> {{ $customer->email }} <br>
                                            <b>Customer Type &nbsp; :</b> {{ $customer->customer_type }} <br>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="fal">
                                            <b>Address:</b> <br> {{ $customer->address }} <br>
                                            <b>City:</b>  {{ $customer->city }} <br>
                                            <b>Country:</b>  {{ $customer->country }} <br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $customer->name }}</p>
                                <p><strong>Email:</strong> {{ $customer->email }}</p>
                                <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                                <!-- Add other customer fields as necessary -->
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong> {{ $customer->address }}</p>
                                <p><strong>City:</strong> {{ $customer->city }}</p>
                                <p><strong>Country:</strong> {{ $customer->country }}</p>
                                <!-- Add other customer fields as necessary -->
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="card">
                    <div class="card-header">Customer Transactions</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <!-- Add other transaction fields as necessary -->
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_array($customer->sales))
                                  @foreach ($customer->sales as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->transaction_date }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>{{ $transaction->status }}</td>
                                        <!-- Add other transaction fields as necessary -->
                                    </tr>
                                  @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
