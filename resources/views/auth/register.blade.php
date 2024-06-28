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

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('register') }}" class="mb-6">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control" name="name" :value="old('name')" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" :value="old('email')" required />
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required />
                        </div>

                        <!-- Next Button -->
                        <div class="d-grid gap-2">
                            <x-button class="btn btn-dark btn-block" type="submit" name="action" value="next">
                                {{ __('Next') }}
                            </x-button>
                        </div>

                        <div class="mt-3">
                            <a class="btn btn-link" href="{{ route('login') }}">{{ __('Already registered? Login here') }}</a>
                        </div>
                    </form>
                </x-auth-card>
            </div>
        </div>
    </div>
</x-guest-layout>
