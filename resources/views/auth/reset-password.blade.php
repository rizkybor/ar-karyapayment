<x-authentication-layout>
    <x-validation-errors class="mb-4" />

    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Create New Password') }}</h1>
    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
        Silahkan perbaharui password anda disini.
    </p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->token }}">
        <input type="hidden" name="email" value="{{ request()->email }}">


        {{-- <div class="block">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
        </div> --}}

        <div class="block">
            <x-label for="email" value="{{ __('Email') }}" />
            <div class="mt-1 w-full px-4 py-2 bg-gray-100 rounded border border-gray-300 text-gray-700">
                {{ request()->email }}
            </div>
        </div>

        {{-- <div class="mt-4">
            <x-label for="password" value="{{ __('New Password') }}" />
            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
        </div> --}}

        <div class="mt-4">
            <x-label for="password" value="{{ __('New Password') }}" />
            <x-input-password id="password" name="password" required autocomplete="new-password"
                class="form-input w-full mt-1" />
            <div id="password-strength-text" class="mt-2 text-sm font-medium"></div>
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input-password id="password_confirmation" name="password_confirmation" required
                autocomplete="new-password" class="form-input w-full mt-1" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button>
                {{ __('Reset Password') }}
            </x-button>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthText = document.getElementById('password-strength-text');

            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                const strength = getPasswordStrength(val);

                strengthText.textContent = strength.label;
                strengthText.style.color = strength.color;
            });

            function getPasswordStrength(password) {
                const hasLower = /[a-z]/.test(password);
                const hasUpper = /[A-Z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSymbol = /[\W_]/.test(password);
                const isLongEnough = password.length >= 8;

                const passed = [hasLower, hasUpper, hasNumber, hasSymbol].filter(Boolean).length;

                if (!isLongEnough) {
                    return {
                        label: 'Terlalu pendek (min. 8 karakter)',
                        color: 'red'
                    };
                }

                switch (passed) {
                    case 0:
                    case 1:
                        return {
                            label: 'Lemah', color: 'red'
                        };
                    case 2:
                        return {
                            label: 'Cukup', color: 'orange'
                        };
                    case 3:
                        return {
                            label: 'Kuat', color: 'blue'
                        };
                    case 4:
                        return {
                            label: 'Sangat Kuat', color: 'green'
                        };
                }
            }
        });
    </script>
</x-authentication-layout>
