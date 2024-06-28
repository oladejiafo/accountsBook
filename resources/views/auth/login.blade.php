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

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-43" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-3" :errors="$errors" />

                    <form method="POST" action="{{ route('login') }}" class="mb-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" :value="old('email')" required autofocus />
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input border border-gray-300 text-indigo-600 h-5 w-5 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" style="border-color: aqua !important;" type="checkbox" id="remember_me" name="remember">
                            <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                        </div>

                        <div class="d-grid gap-2">
                            <x-button class="btn btn-dark btn-block"> 
                                {{ __('Log in') }}
                            </x-button>
                        </div>

                        <div class="mt-3">
                            <a class="btn btn-link" href="{{ route('register') }}">{{ __('New Here? Register') }}</a>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                            @endif
                        </div>
                    </form>
                </x-auth-card>
            </div>
        </div>
    </div>
</x-guest-layout>
