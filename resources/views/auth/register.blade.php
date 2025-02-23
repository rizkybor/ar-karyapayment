<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Create your Account') }}</h1>
    <!-- Form -->
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="space-y-4">
            {{-- full name --}}
            <div>
                <x-label for="name">{{ __('Full Name') }} <span class="text-red-500">*</span></x-label>
                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus
                    autocomplete="name" />
            </div>

            {{-- nip --}}
            <div>
                <x-label for="nip">{{ __('NIP') }} <span class="text-red-500">*</span></x-label>
                <x-input id="nip" type="number" name="nip" :value="old('nip')" required autofocus
                    autocomplete="nip" oninput="limitNIPLength(this)" />
                <x-input-error for="nip" class="mt-2" />
            </div>
            <!-- batasi input NIP -->
            <script>
                function limitNIPLength(input) {
                    // Batasi panjang input menjadi 8 digit
                    if (input.value.length > 8) {
                        input.value = input.value.slice(0, 8);
                    }
                }
            </script>

            {{-- email --}}
            <div>
                <x-label for="email">{{ __('Email Address') }} <span class="text-red-500">*</span></x-label>
                <x-input id="email" type="email" name="email" :value="old('email')" required />
            </div>

            {{-- password --}}
            <div>
                <!-- Label dengan tanda bintang merah -->
                <x-label for="password">
                    {{ __('Password') }} <span class="text-red-500">*</span>
                </x-label>

                <!-- Input Password -->
                <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            </div>

            {{-- confirm password --}}
            <div>
                <!-- Label dengan tanda bintang merah -->
                <x-label for="password_confirmation">
                    {{ __('Confirm Password') }} <span class="text-red-500">*</span>
                </x-label>

                <!-- Input Confirm Password -->
                <x-input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" />
            </div>

            {{-- department --}}
            <div>
                <x-label for="department">{{ __('Department') }} <span class="text-red-500">*</span></x-label>
                <x-input id="department" type="text" name="department" :value="old('department')" required autofocus
                    autocomplete="nip" />
            </div>

            {{-- position --}}
            <div>
                <x-label for="position">{{ __('Position') }} <span class="text-red-500">*</span></x-label>
                <x-input id="position" type="text" name="position" :value="old('position')" required autofocus
                    autocomplete="position" />
            </div>

            {{-- role --}}
            <div class="col-span-6 sm:col-span-4">
                <!-- Label -->
                <x-label for="role">{{ __('Role') }} <span class="text-red-500">*</span></x-label>

                <!-- Dropdown Select -->
                <select id="role" name="role"
                    class="mt-1 block w-full form-select rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    wire:model.live="state.role" required autofocus>
                    <option value="">Pilih Role</option>
                    <option value="maker">Maker</option>
                    <option value="kadiv">Kadiv</option>
                    <option value="bendahara">Bendahara</option>
                    <option value="manager_anggaran">Manager Anggaran</option>
                    <option value="direktur_keuangan">Direktur Keuangan</option>
                    <option value="pajak">Pajak</option>
                </select>

                <!-- Error Message -->
                <x-input-error for="role" class="mt-2" />
            </div>

            {{-- employee status --}}
            <div>
                <x-label for="employee_status">{{ __('Employee Status') }} <span
                        class="text-red-500">*</span></x-label>
                <x-input id="employee_status" type="text" name="employee_status" :value="old('employee_status')" required autofocus
                    autocomplete="employee_status" />
            </div>

            {{-- gender --}}
            <div class="col-span-6 sm:col-span-4">
                <!-- Label -->
                <x-label for="gender">{{ __('Gender') }} <span class="text-red-500">*</span></x-label>

                <!-- Radio Button Group -->
                <div class="mt-1 space-y-2">
                    <!-- Pria -->
                    <label class="inline-flex items-center">
                        <input type="radio" id="gender_pria" name="gender" value="pria"
                            class="form-radio h-4 w-4 transition duration-150 ease-in-out"
                            wire:model.live="state.gender" required autofocus />
                        <span class="ml-2">Pria</span>
                    </label>

                    <!-- Wanita -->
                    <label class="inline-flex items-center">
                        <input type="radio" id="gender_wanita" name="gender" value="wanita"
                            class="form-radio h-4 w-4 transition duration-150 ease-in-out"
                            wire:model.live="state.gender" required autofocus />
                        <span class="ml-2">Wanita</span>
                    </label>
                </div>
            </div>

            {{-- identity number --}}
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
