<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Create your Account') }}</h1>
    <!-- Form -->
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="name">{{ __('Full Name') }} <span class="text-red-500">*</span></x-label>
                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus
                    autocomplete="name" />
            </div>

            <div>
                <x-label for="nip">{{ __('NIP') }} <span class="text-red-500">*</span></x-label>
                <x-input id="nip" type="number" name="nip" :value="old('nip')" required autofocus
                    autocomplete="nip" />
            </div>

            <div>
                <x-label for="email">{{ __('Email Address') }} <span class="text-red-500">*</span></x-label>
                <x-input id="email" type="email" name="email" :value="old('email')" required />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" />
            </div>
            <div>
                <x-label for="department">{{ __('Department') }} <span class="text-red-500">*</span></x-label>
                <x-input id="department" type="text" name="department" :value="old('department')" required autofocus
                    autocomplete="nip" />
            </div>
            <div>
                <x-label for="position">{{ __('Position') }} <span class="text-red-500">*</span></x-label>
                <x-input id="position" type="text" name="position" :value="old('position')" required autofocus
                    autocomplete="position" />
            </div>
            <div>
                <x-label for="role">{{ __('Role') }} <span class="text-red-500">*</span></x-label>
                <x-input id="role" type="text" name="role" :value="old('role')" required autofocus
                    autocomplete="role" />
            </div>
            <div>
                <x-label for="employee_status">{{ __('Employee Status') }} <span
                        class="text-red-500">*</span></x-label>
                <x-input id="employee_status" type="text" name="employee_status" :value="old('employee_status')" required autofocus
                    autocomplete="employee_status" />
            </div>
            <div>
                <x-label for="gender">{{ __('Gender') }} <span class="text-red-500">*</span></x-label>
                <x-input id="gender" type="text" name="gender" :value="old('gender')" required autofocus
                    autocomplete="gender" />
            </div>
            <div>
                <x-label for="identity_number">{{ __('Identity Number') }} <span
                        class="text-red-500">*</span></x-label>
                <x-input id="identity_number" type="text" name="identity_number" :value="old('identity_number')" required autofocus
                    autocomplete="nip" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            <div class="mr-1">
                <label class="flex items-center" name="newsletter" id="newsletter">
                    <input type="checkbox" class="form-checkbox" />
                    <span class="text-sm ml-2">Email me about product news.</span>
                </label>
            </div>
            <x-button>
                {{ __('Sign Up') }}
            </x-button>
        </div>
        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-6">
                <label class="flex items-start">
                    <input type="checkbox" class="form-checkbox mt-1" name="terms" id="terms" />
                    <span class="text-sm ml-2">
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' =>
                                '<a target="_blank" href="' .
                                route('terms.show') .
                                '" class="text-sm underline hover:no-underline">' .
                                __('Terms of Service') .
                                '</a>',
                            'privacy_policy' =>
                                '<a target="_blank" href="' .
                                route('policy.show') .
                                '" class="text-sm underline hover:no-underline">' .
                                __('Privacy Policy') .
                                '</a>',
                        ]) !!}
                    </span>
                </label>
            </div>
        @endif
    </form>
    <x-validation-errors class="mt-4" />
    <!-- Footer -->
    <div class="pt-5 mt-6 border-t border-gray-100 dark:border-gray-700/60">
        <div class="text-sm">
            {{ __('Have an account?') }} <a
                class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400"
                href="{{ route('login') }}">{{ __('Sign In') }}</a>
        </div>
    </div>
</x-authentication-layout>
