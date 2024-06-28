<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <x-auth-card>
                    <x-slot name="logo">
                        <div class="text-center mb-4"> <!-- Center the logo -->
                            <a href="/">
                                <img src="{{ asset('images/akontledger_logo.png') }}" class="w-20 h-20" alt="Logo">
                            </a>
                        </div>
                    </x-slot>

                    <div class="mb-4 text-sm text-gray-600">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('password.email') }}" class="mb-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" :value="old('email')" required autofocus />
                        </div>

                        <div class="d-grid gap-2">
                            <x-button class="btn btn-dark btn-block">
                                {{ __('Email Password Reset Link') }}
                            </x-button>
                        </div>
                    </form>
                </x-auth-card>
            </div>
        </div>
    </div>
</x-guest-layout>
