<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <x-auth-card>
                    <x-slot name="logo">
                        <div class="text-center mb-4"> <!-- Center the logo -->
                            <a href="/">
                                <img src="{{ asset('images/akontledger_logo.png') }}" class="w-20 h-20" alt="Logo">
                            </a>
                        </div>
                    </x-slot>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('register.company') }}" class="mb-6">
                        @csrf

                        <!-- Company Name -->
                        <div class="mb-3">
                            <label for="company_name" class="form-label">{{ __('Company Name') }}</label>
                            <input id="company_name" type="text" class="form-control" name="company_name" :value="old('company_name')" required autofocus />
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <input id="address" type="text" class="form-control" name="address" :value="old('address')" required />
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" :value="old('email')" required />
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">{{ __('Phone') }}</label>
                            <input id="phone" type="text" class="form-control" name="phone" :value="old('phone')" required />
                        </div>

                        <!-- Website -->
                        <div class="mb-3">
                            <label for="website" class="form-label">{{ __('Website') }}</label>
                            <input id="website" type="text" class="form-control" name="website" :value="old('website')" />
                        </div>

                        <!-- Default Currency -->
                        <div class="mb-3">
                            <label for="currency" class="form-label">{{ __('Default Currency') }}</label>
                            <select id="currency" class="form-select" name="currency" required>
                                <option value="">Select Default Currency</option>
                                @foreach($currencies as $id => $acronym)
                                    <option value="{{ $id }}">{{ $acronym }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subscription Type -->
                        <div class="mb-3">
                            <label for="subscription_type" class="form-label">{{ __('Subscription Type') }}</label>
                            <select id="subscription_type" class="form-select" name="subscription_type" required>
                                <option value="">Select Subscription Type</option>
                                <option value="monthly">Monthly</option>
                                <option value="annual">Annual</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>

                        <!-- Business Type -->
                        <div class="mb-3">
                            <label for="business_type" class="form-label">{{ __('Business Type') }}</label>
                            <select id="business_type" class="form-select" name="business_type" required>
                                <option value="">Select Business Type</option>
                                <option value="product">Product Based</option>
                                <option value="service">Service Based</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                                {{ __('Go to Login') }}
                            </a>
                            <x-button class="ml-4 btn btn-dark">
                                {{ __('Register') }}
                            </x-button>
                        </div>
                    </form>
                </x-auth-card>
            </div>
        </div>
    </div>
</x-guest-layout>
