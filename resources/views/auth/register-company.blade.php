<!-- resources/views/auth/company.blade.php -->

<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register.company') }}">
            @csrf

            <!-- Company Name -->
            <div>
                <x-label for="company_name" :value="__('Company Name')" />
                <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
            </div>

           
            <!-- Address -->
            <div class="mt-4">
                <x-label for="address" :value="__('Address')" />
                <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>
            <!-- Phone -->
            <div class="mt-4">
                <x-label for="phone" :value="__('Phone')" />
                <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            </div>
            <!-- Website -->
            <div class="mt-4">
                <x-label for="website" :value="__('Website')" />
                <x-input id="website" class="block mt-1 w-full" type="text" name="website" :value="old('website')" />
            </div>

            {{-- <!-- Subscription Type -->
            <div class="mt-4">
                <x-label for="subscription_type" :value="__('Subscription Type')" />
                <x-select id="subscription_type" class="block mt-1 w-full" name="subscription_type" required>
                    <option value="">Select Subscription Type</option>
                    <option value="monthly">Monthly</option>
                    <option value="annual">Annual</option>
                    <!-- Add more options as needed -->
                </x-select>
            </div>

            <!-- Business Type -->
            <div class="mt-4">
                <x-label for="business_type" :value="__('Business Type')" />
                <x-select id="business_type" class="block mt-1 w-full" name="business_type" required>
                    <option value="">Select Business Type</option>
                    <option value="product">Product Based</option>
                    <option value="service">Service Based</option>
                    <!-- Add more options as needed -->
                </x-select>
            </div> --}}

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
